<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductVariationController extends Controller
{
    public function index(Product $product)
    {
        return response()->json([
            'data' => $product->variations()
                ->with('attributeValues.attribute')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function generate(Request $request, Product $product)
    {
        $request->validate([
            'groups'            => 'required|array|min:1',
            'groups.*'          => 'array|min:1',
            'groups.*.*'        => 'integer|exists:attribute_values,id',
            'default_price'     => 'required|numeric|min:0',
            'default_stock'     => 'required|integer|min:0',
        ]);

        $combinations = $this->cartesian($request->groups);
        $created = 0;

        foreach ($combinations as $combo) {
            $variation = $product->variations()->create([
                'price'     => $request->default_price,
                'stock'     => $request->default_stock,
                'is_active' => true,
            ]);
            $variation->attributeValues()->sync($combo);
            $created++;
        }

        return response()->json([
            'message' => "{$created} variation(s) generated.",
            'data'    => $product->variations()
                ->with('attributeValues.attribute')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function update(Request $request, Product $product, ProductVariation $variation)
    {
        $data = $request->validate([
            'sku'            => 'nullable|string|max:100',
            'price'          => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'is_active'      => 'boolean',
        ]);

        $variation->update($data);

        return response()->json([
            'data'    => $variation->load('attributeValues.attribute'),
            'message' => 'Variation updated.',
        ]);
    }

    public function uploadImage(Request $request, Product $product, ProductVariation $variation)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($variation->image) {
            Storage::disk('public')->delete($variation->image);
        }

        $variation->update([
            'image' => $request->file('image')->store('products/variations', 'public'),
        ]);

        return response()->json([
            'data'    => $variation->load('attributeValues.attribute'),
            'message' => 'Image uploaded.',
        ]);
    }

    public function removeImage(Product $product, ProductVariation $variation)
    {
        if ($variation->image) {
            Storage::disk('public')->delete($variation->image);
            $variation->update(['image' => null]);
        }

        return response()->json(['message' => 'Image removed.']);
    }

    public function destroy(Product $product, ProductVariation $variation)
    {
        if ($variation->image) {
            Storage::disk('public')->delete($variation->image);
        }

        $variation->delete();

        return response()->json(['message' => 'Variation deleted.']);
    }

    private function cartesian(array $groups): array
    {
        $result = [[]];
        foreach ($groups as $values) {
            $tmp = [];
            foreach ($result as $prev) {
                foreach ($values as $v) {
                    $tmp[] = array_merge($prev, [(int) $v]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}
