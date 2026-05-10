<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images'])->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        return response()->json(
            $query->paginate($request->integer('per_page', 15))
        );
    }

    public function store(Request $request)
    {
        $type = $request->input('type', 'simple');

        $data = $request->validate([
            'type'           => 'required|in:simple,variable,grouped',
            'category_id'    => 'nullable|exists:categories,id',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => $type === 'simple'   ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock'          => $type === 'simple'   ? 'required|integer|min:0' : 'nullable|integer|min:0',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'emoji'          => 'nullable|string|max:20',
            'badge'          => 'nullable|string|max:20',
            'is_active'      => 'boolean',
        ]);

        $data['image'] = $request->hasFile('image')
            ? $this->storeVerifiedImage($request->file('image'), 'products')
            : null;

        $data['slug'] = Str::slug($data['name']).'-'.Str::random(5);

        $product = Product::create($data);

        Log::info('admin.product.created', ['admin_id' => auth()->id(), 'product_id' => $product->id, 'name' => $product->name]);

        return response()->json(
            ['data' => $product->load(['category', 'images']), 'message' => 'Product created.'],
            201
        );
    }

    public function show(Product $product)
    {
        return response()->json(['data' => $product->load(['category', 'images'])]);
    }

    public function update(Request $request, Product $product)
    {
        $type = $request->input('type', $product->type);

        $data = $request->validate([
            'type'           => 'required|in:simple,variable,grouped',
            'category_id'    => 'nullable|exists:categories,id',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => $type === 'simple'   ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock'          => $type === 'simple'   ? 'required|integer|min:0' : 'nullable|integer|min:0',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'emoji'          => 'nullable|string|max:20',
            'badge'          => 'nullable|string|max:20',
            'is_active'      => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $this->storeVerifiedImage($request->file('image'), 'products');
        } elseif ($request->boolean('remove_image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = null;
        } else {
            unset($data['image']);
        }

        $product->update($data);

        Log::info('admin.product.updated', ['admin_id' => auth()->id(), 'product_id' => $product->id, 'name' => $product->name]);

        return response()->json(['data' => $product->load(['category', 'images']), 'message' => 'Product updated.']);
    }

    private function storeVerifiedImage(\Illuminate\Http\UploadedFile $file, string $directory): string
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array(mime_content_type($file->getRealPath()), $allowed, true)) {
            abort(422, 'Invalid image file.');
        }
        return $file->store($directory, 'public');
    }

    public function destroy(Product $product)
    {
        Log::info('admin.product.deleted', ['admin_id' => auth()->id(), 'product_id' => $product->id, 'name' => $product->name]);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted.']);
    }
}
