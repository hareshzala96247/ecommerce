<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $path     = $request->file('image')->store('products/gallery', 'public');
        $maxOrder = $product->images()->max('sort_order') ?? -1;

        $image = $product->images()->create([
            'path'       => $path,
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json(['data' => $image, 'message' => 'Image uploaded.'], 201);
    }

    public function destroy(Product $product, ProductImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['message' => 'Image deleted.']);
    }
}
