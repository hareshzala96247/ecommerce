<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@shopvue.com'],
            ['name' => 'Admin', 'password' => Hash::make('admin123'), 'role' => 'admin']
        );

        // Sample customer
        $customer = User::updateOrCreate(
            ['email' => 'john@example.com'],
            ['name' => 'John Doe', 'password' => Hash::make('password'), 'role' => 'user']
        );

        // Categories
        $cats = [];
        foreach ([
            ['Fashion',       '👗', 'Clothing and accessories'],
            ['Electronics',   '📱', 'Gadgets and devices'],
            ['Home & Living', '🛋️', 'Furniture and décor'],
            ['Sports',        '⚽', 'Sports equipment'],
            ['Beauty',        '💄', 'Cosmetics and skincare'],
            ['Books',         '📚', 'Books and literature'],
        ] as [$name, $icon, $desc]) {
            $cats[$name] = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'slug' => Str::slug($name), 'icon' => $icon, 'description' => $desc]
            );
        }

        // Products
        $prods = [];
        foreach ([
            ['Premium Wireless Headphones', 'Electronics',   89.99,  129.99, 50,  '🎧', 'Hot'],
            ['Casual Linen Summer Dress',   'Fashion',       45.00,  75.00,  120, '👗', 'Sale'],
            ['Smart Fitness Watch Pro',     'Electronics',   149.99, null,   35,  '⌚', 'New'],
            ['Leather Crossbody Bag',       'Fashion',       69.99,  99.99,  60,  '👜', 'Trending'],
            ['Minimalist Table Lamp',       'Home & Living', 34.99,  null,   80,  '💡', 'New'],
            ['Running Sneakers X200',       'Sports',        79.99,  110.00, 95,  '👟', 'Hot'],
            ['Vitamin C Serum 30ml',        'Beauty',        24.99,  null,   200, '🧴', 'Trending'],
            ['Portable Bluetooth Speaker',  'Electronics',   49.99,  79.99,  70,  '🔊', 'Sale'],
            ['Oversized Knit Sweater',      'Fashion',       55.00,  null,   45,  '🧥', 'New'],
        ] as [$name, $cat, $price, $orig, $stock, $emoji, $badge]) {
            $prods[] = Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id'    => $cats[$cat]->id,
                    'name'           => $name,
                    'slug'           => Str::slug($name),
                    'price'          => $price,
                    'original_price' => $orig,
                    'stock'          => $stock,
                    'emoji'          => $emoji,
                    'badge'          => $badge,
                    'is_active'      => true,
                ]
            );
        }

        // Sample orders
        foreach ([
            ['Alice Johnson', 'alice@example.com', 'delivered',  [[0,1],[1,2]]],
            ['Bob Smith',     'bob@example.com',   'shipped',    [[2,1]]],
            ['Carol White',   'carol@example.com', 'processing', [[3,1],[5,2]]],
            ['David Brown',   'david@example.com', 'pending',    [[6,3]]],
            ['Eva Martinez',  'eva@example.com',   'pending',    [[7,1],[4,1]]],
        ] as [$name, $email, $status, $items]) {
            $total = collect($items)->sum(fn($i) => $prods[$i[0]]->price * $i[1]);

            $order = Order::create([
                'user_id'        => $customer->id,
                'customer_name'  => $name,
                'customer_email' => $email,
                'total'          => $total,
                'status'         => $status,
            ]);

            foreach ($items as [$idx, $qty]) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $prods[$idx]->id,
                    'product_name' => $prods[$idx]->name,
                    'price'        => $prods[$idx]->price,
                    'quantity'     => $qty,
                ]);
            }
        }
    }
}
