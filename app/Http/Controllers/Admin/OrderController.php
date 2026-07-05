<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request){
        $query = Order::with(['user','table', 'payment'])->orderBy('created_at', 'desc');

        //filter by status
        if($request->status){
            $query->where('status',$request->status);
        }

        // Filter by order type
        if ($request->order_type) {
            $query->where('order_type', $request->order_type);
        }

        // Filter by payment method
        if ($request->payment_method) {
            $query->whereHas('payment', function($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            });
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by search (order code)
        if ($request->search) {
            $query->where('order_code', 'like', '%' . $request->search . '%');
        }

        $orders = $query->paginate(15)->withQueryString();

        // Summary
        $summary = [
            'total_orders'    => $query->count(),
            'total_revenue'   => Order::where('status', 'completed')
                                      ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
                                      ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
                                      ->sum('final_amount'),
            'total_completed' => Order::where('status', 'completed')
                                      ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
                                      ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
                                      ->count(),
            'total_cancelled' => Order::where('status', 'cancelled')
                                      ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
                                      ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
                                      ->count(),
        ];

        return view('admin.orders.index', compact('orders', 'summary'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'table', 'payment', 'items.menu']);
        return view('admin.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        if ($order->status === 'completed') {
            return redirect()->route('admin.orders.index')
                             ->with('error', 'Order yang sudah selesai tidak bisa dihapus.');
        }

        $order->delete();
        return redirect()->route('admin.orders.index')
                         ->with('success', 'Order berhasil dihapus.');
    }
}