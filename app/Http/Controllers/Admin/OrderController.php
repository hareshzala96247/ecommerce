<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(Order::latest()->paginate(15));
    }

    public function show(Order $order)
    {
        return response()->json(['data' => $order->load('items.product:id,image,emoji', 'items.variation:id,image')]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $previous = $order->status;
        $order->update(['status' => $request->status]);

        Log::info('admin.order.status_updated', [
            'admin_id' => auth()->id(),
            'order_id' => $order->id,
            'from'     => $previous,
            'to'       => $request->status,
        ]);

        return response()->json(['data' => $order, 'message' => 'Order status updated.']);
    }
}
