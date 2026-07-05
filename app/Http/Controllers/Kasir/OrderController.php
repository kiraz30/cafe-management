<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'table', 'payment'])
                      ->whereDate('created_at', today())
                      ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by order type
        if ($request->order_type) {
            $query->where('order_type', $request->order_type);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Summary hari ini
        $summary = [
            'total_orders'  => Order::whereDate('created_at', today())->count(),
            'total_revenue' => Order::where('status', 'completed')
                                    ->whereDate('created_at', today())
                                    ->sum('final_amount'),
            'total_completed' => Order::where('status', 'completed')
                                      ->whereDate('created_at', today())
                                      ->count(),
            'total_cancelled' => Order::where('status', 'cancelled')
                                      ->whereDate('created_at', today())
                                      ->count(),
        ];

        return view('kasir.orders.index', compact('orders', 'summary'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'table', 'payment', 'items.menu']);
        return view('kasir.orders.show', compact('order'));
    }
}
