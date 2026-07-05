<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $summary = [
            'total_orders'    => Order::whereDate('created_at', today())->count(),
            'total_completed' => Order::where('status', 'completed')
                                      ->whereDate('created_at', today())
                                      ->count(),
            'total_cancelled' => Order::where('status', 'cancelled')
                                      ->whereDate('created_at', today())
                                      ->count(),
            'total_revenue'   => Order::where('status', 'completed')
                                      ->whereDate('created_at', today())
                                      ->sum('final_amount'),
        ];

        $recent_orders = Order::with(['table', 'payment'])
                              ->whereDate('created_at', today())
                              ->orderBy('created_at', 'desc')
                              ->limit(5)
                              ->get();

        return view('kasir.dashboard', compact('summary', 'recent_orders'));
    }
}