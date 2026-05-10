<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'stats' => [
                'products'       => Product::count(),
                'categories'     => Category::count(),
                'orders'         => Order::count(),
                'users'          => User::where('role', 'user')->count(),
                'revenue'        => (float) Order::whereNotIn('status', ['cancelled'])->sum('total'),
                'pending_orders' => Order::where('status', 'pending')->count(),
            ],
            'recent_orders' => Order::with('items')->latest()->take(5)->get(),
        ]);
    }
}
