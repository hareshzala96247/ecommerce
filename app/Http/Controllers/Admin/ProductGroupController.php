<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductGroupController extends Controller
{
    public function index(Product $product)
    {
        return response()->json([
            'data' => $product->groupedProducts()->with('category')->get(),
        ]);
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'child_id' => 'required|integer|exists:products,id',
        ]);

        if ($request->child_id == $product->id) {
            return response()->json(['message' => 'Cannot add a product to its own group.'], 422);
        }

        if ($product->groupedProducts()->where('child_id', $request->child_id)->exists()) {
            return response()->json(['message' => 'Product is already in this group.'], 422);
        }

        $product->groupedProducts()->attach($request->child_id, ['sort_order' => 0]);

        return response()->json([
            'data'    => $product->groupedProducts()->with('category')->get(),
            'message' => 'Product added to group.',
        ]);
    }

    public function destroy(Product $product, int $childId)
    {
        $product->groupedProducts()->detach($childId);

        return response()->json(['message' => 'Product removed from group.']);
    }
}
