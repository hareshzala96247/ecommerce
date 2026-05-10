<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
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

        $resolvedItems = [];
        foreach ($data['items'] as $item) {
            $vid = $item['variation_id'] ?? null;
            if ($vid && $variations->has($vid)) {
                $variation = $variations[$vid];
                if ($variation->product_id !== $item['product_id']) {
                    throw ValidationException::withMessages(['items' => ['Invalid product configuration.']]);
                }
                if ($variation->stock !== null && $variation->stock < $item['qty']) {
                    throw ValidationException::withMessages(['items' => ["Insufficient stock for: {$item['name']}"]]);
                }
                $price = (float) $variation->price;
            } elseif ($products->has($item['product_id'])) {
                $product = $products[$item['product_id']];
                if ($product->stock !== null && $product->stock < $item['qty']) {
                    throw ValidationException::withMessages(['items' => ["Insufficient stock for: {$item['name']}"]]);
                }
                $price = (float) $product->price;
            } else {
                throw ValidationException::withMessages(['items' => ['Unable to verify price for one or more items.']]);
            }
            $resolvedItems[] = ['price' => $price] + $item;
        }

        $subtotal   = collect($resolvedItems)->sum(fn ($i) => $i['price'] * $i['qty']);
        $shipping   = $subtotal >= 50 ? 0 : 5.99;
        $total      = round($subtotal + $shipping, 2);
        $guestToken = auth()->check() ? null : Str::random(40);

        $order = Order::create([
            'user_id'        => auth()->id(),
            'guest_token'    => $guestToken,
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
                'product_name' => $item['name'],
                'price'        => $item['price'],
                'quantity'     => $item['qty'],
            ]);
        }

        $response = ['order' => $order->load('items')];
        if ($guestToken) {
            $response['guest_token'] = $guestToken;
        }

        return response()->json($response, 201);
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items')
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
            $token = (string) $request->query('token', '');
            if (!$order->guest_token || !hash_equals($order->guest_token, $token)) {
                return response()->json(['message' => 'Not found.'], 404);
            }
        }

        return response()->json(['data' => $order->load('items')]);
    }
}
