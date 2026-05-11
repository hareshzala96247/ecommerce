<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;

class FavoriteController extends Controller
{
    public function index()
    {
        if (! auth()->check()) {
            return response()->json(['data' => []]);
        }

        $products = Favorite::where('user_id', auth()->id())
            ->with(['product' => fn ($q) => $q
                ->with('category')
                ->where('is_active', true)
                ->withMin('variations', 'price')
                ->withMax('variations', 'price')])
            ->get()
            ->pluck('product')
            ->filter()
            ->values();

        return response()->json(['data' => $products]);
    }

    public function toggle(Product $product)
    {
        if (! auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! $product->is_active) {
            abort(404);
        }

        $existing = Favorite::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['favorited' => false]);
        }

        Favorite::create(['user_id' => auth()->id(), 'product_id' => $product->id]);
        return response()->json(['favorited' => true]);
    }
}
