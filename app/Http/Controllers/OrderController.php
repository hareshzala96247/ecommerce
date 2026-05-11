<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmedEmail;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255',
            'phone'                 => 'nullable|string|max:30',
            'address'               => 'required|string|max:255',
            'city'                  => 'required|string|max:100',
            'state'                 => 'nullable|string|max:100',
            'zip'                   => 'required|string|max:20',
            'country'               => 'required|string|max:100',
            'notes'                 => 'nullable|string|max:1000',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => ['required', 'integer', Rule::exists('products', 'id')->where('is_active', true)],
            'items.*.variation_id'  => ['nullable', 'integer', Rule::exists('product_variations', 'id')->where('is_active', true)],
            'items.*.name'          => 'required|string|max:500',
            'items.*.qty'           => 'required|integer|min:1',
        ]);

        // Resolve canonical prices from DB — client-submitted prices are ignored entirely
        $products   = Product::whereIn('id', collect($data['items'])->pluck('product_id')->unique())->get()->keyBy('id');
        $variations = ProductVariation::whereIn('id', collect($data['items'])->pluck('variation_id')->filter()->unique())->get()->keyBy('id');

        // Build resolved items with server-side prices; stock check deferred to the transaction
        $resolvedItems = [];
        foreach ($data['items'] as $item) {
            $vid = $item['variation_id'] ?? null;
            if ($vid && $variations->has($vid)) {
                $variation = $variations[$vid];
                if ($variation->product_id !== $item['product_id']) {
                    throw ValidationException::withMessages(['items' => ['Invalid product configuration.']]);
                }
                $price = (float) $variation->price;
            } elseif ($products->has($item['product_id'])) {
                $price = (float) $products[$item['product_id']]->price;
            } else {
                throw ValidationException::withMessages(['items' => ['Unable to verify price for one or more items.']]);
            }
            $resolvedItems[] = ['price' => $price] + $item;
        }

        $subtotal   = collect($resolvedItems)->sum(fn ($i) => $i['price'] * $i['qty']);
        $shipping   = $subtotal >= 50 ? 0 : 5.99;
        $total      = round($subtotal + $shipping, 2);
        $guestToken = auth()->check() ? null : Str::random(40);

        $order = DB::transaction(function () use ($data, $guestToken, $total, $resolvedItems) {
            // Re-fetch with a write lock so concurrent orders cannot both pass the stock check
            $productIds   = collect($resolvedItems)->pluck('product_id')->unique()->values()->all();
            $variationIds = collect($resolvedItems)->pluck('variation_id')->filter()->unique()->values()->all();

            $lockedProducts   = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');
            $lockedVariations = $variationIds
                ? ProductVariation::whereIn('id', $variationIds)->lockForUpdate()->get()->keyBy('id')
                : collect();

            // Authoritative stock check inside the lock
            foreach ($resolvedItems as $item) {
                $vid = $item['variation_id'] ?? null;
                if ($vid && $lockedVariations->has($vid)) {
                    $v = $lockedVariations[$vid];
                    if ($v->stock !== null && $v->stock < $item['qty']) {
                        throw ValidationException::withMessages(['items' => ["Insufficient stock for: {$item['name']}"]]);
                    }
                } elseif ($lockedProducts->has($item['product_id'])) {
                    $p = $lockedProducts[$item['product_id']];
                    if ($p->stock !== null && $p->stock < $item['qty']) {
                        throw ValidationException::withMessages(['items' => ["Insufficient stock for: {$item['name']}"]]);
                    }
                }
            }

            $order = Order::create([
                'user_id'        => auth()->id(),
                'guest_token'    => $guestToken ? hash('sha256', $guestToken) : null,
                'customer_name'  => $data['name'],
                'customer_email' => $data['email'],
                'phone'          => $data['phone'] ?? null,
                'address'        => $data['address'],
                'city'           => $data['city'],
                'state'          => $data['state'] ?? null,
                'zip'            => $data['zip'],
                'country'        => $data['country'],
                'total'          => $total,
                'status'         => 'pending',
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($resolvedItems as $item) {
                $order->items()->create([
                    'product_id'   => $item['product_id'],
                    'variation_id' => $item['variation_id'] ?? null,
                    'product_name' => $item['name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['qty'],
                ]);

                $vid = $item['variation_id'] ?? null;
                if ($vid && $lockedVariations->has($vid)) {
                    $lockedVariations[$vid]->decrement('stock', $item['qty']);
                } elseif ($lockedProducts->has($item['product_id'])) {
                    $lockedProducts[$item['product_id']]->decrement('stock', $item['qty']);
                }
            }

            return $order;
        });

        Log::info('order.created', [
            'order_id'       => $order->id,
            'user_id'        => auth()->id(),
            'total'          => $order->total,
            'customer_email' => $order->customer_email,
            'is_guest'       => $guestToken !== null,
        ]);

        try {
            Mail::to($order->customer_email)->queue(new OrderConfirmedEmail($order));
        } catch (\Throwable $e) {
            Log::warning('order_confirmed_email_failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        $response = ['order' => $order->load('items.product:id,image,emoji', 'items.variation:id,image')];
        if ($guestToken) {
            // Store in session so the confirmation page doesn't need to expose the token in the URL
            session(['guest_token_' . $order->id => $guestToken]);
            $response['guest_token'] = $guestToken;
        }

        return response()->json($response, 201);
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items.product:id,image,emoji', 'items.variation:id,image')
            ->latest()
            ->get();

        return response()->json(['data' => $orders]);
    }

    public function show(Request $request, Order $order)
    {
        if (auth()->check()) {
            if ($order->user_id && $order->user_id !== auth()->id()) {
                return response()->json(['message' => 'Not found.'], 404);
            }
        } else {
            // Guest orders require the token issued at creation time
            if ($order->user_id !== null) {
                return response()->json(['message' => 'Not found.'], 404);
            }
            // Prefer the session-stored token to avoid URL exposure; fall back to query param
            $token = session('guest_token_' . $order->id) ?? (string) $request->query('token', '');
            if (!$order->guest_token || empty($token) || !hash_equals($order->guest_token, hash('sha256', $token))) {
                return response()->json(['message' => 'Not found.'], 404);
            }
        }

        return response()->json(['data' => $order->load('items.product:id,image,emoji', 'items.variation:id,image')]);
    }
}
