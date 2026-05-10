<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function products(Request $request)
    {
        $query = Product::with('category')
            ->withMin('variations', 'price')
            ->withMax('variations', 'price')
            ->where('is_active', true);

        // Tab/badge filter (homepage tabs)
        if ($request->filled('tab')) {
            $badgeMap = [
                'trending'    => ['Hot', 'Trending'],
                'new'         => ['New'],
                'bestsellers' => ['Sale'],
            ];
            $badges = $badgeMap[$request->tab] ?? null;
            if ($badges) {
                $query->whereIn('badge', $badges);
            }
        }

        // Full-text search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Category filter — by ID or slug
        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        } elseif ($request->filled('category_slug')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category_slug));
        }

        // Sort
        match ($request->query('sort', 'latest')) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name'       => $query->orderBy('name', 'asc'),
            default      => $query->latest(),
        };

        $perPage = $request->integer('per_page', 12);

        return response()->json($query->paginate($perPage));
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        if ($product->type === 'grouped') {
            $product->load('groupedProducts.images');
        } elseif ($product->type === 'variable') {
            $product->load('variations.attributeValues.attribute');
        }

        $related = Product::with('category')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->latest()
            ->limit(4)
            ->get();

        return response()->json([
            'data'    => $product,
            'related' => $related,
        ]);
    }

    public function categories()
    {
        return response()->json([
            'data' => Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->latest()
                ->get(),
        ]);
    }

    public function categoryBySlug($slug)
    {
        $category = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json(['data' => $category]);
    }
}
