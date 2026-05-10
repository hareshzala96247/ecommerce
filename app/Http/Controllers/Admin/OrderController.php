<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedEmail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        // Send notification for all statuses except pending (covered by order confirmation)
        if ($request->status !== 'pending') {
            try {
                Mail::to($order->customer_email)->send(new OrderStatusUpdatedEmail($order));
            } catch (\Throwable $e) {
                Log::warning('order_status_email_failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }
        }

        return response()->json(['data' => $order, 'message' => 'Order status updated.']);
    }
}
