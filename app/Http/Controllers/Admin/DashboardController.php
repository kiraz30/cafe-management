<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ingredient;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // =====================
        // Statistik Hari Ini
        // =====================
        $today = [
            'total_orders'    => Order::whereDate('created_at', today())->count(),
            'total_completed' => Order::where('status', 'completed')
                                      ->whereDate('created_at', today())
                                      ->count(),
            'total_pending'   => Order::where('status', 'pending')
                                      ->whereDate('created_at', today())
                                      ->count(),
            'total_cancelled' => Order::where('status', 'cancelled')
                                      ->whereDate('created_at', today())
                                      ->count(),
            'total_revenue'   => Order::where('status', 'completed')
                                      ->whereDate('created_at', today())
                                      ->sum('final_amount'),
            'total_cash'      => Order::where('status', 'completed')
                                      ->whereDate('created_at', today())
                                      ->whereHas('payment', fn($q) => $q->where('payment_method', 'cash'))
                                      ->sum('final_amount'),
            'total_qris'      => Order::where('status', 'completed')
                                      ->whereDate('created_at', today())
                                      ->whereHas('payment', fn($q) => $q->where('payment_method', 'qris'))
                                      ->sum('final_amount'),
        ];

        // =====================
        // Statistik Bulan Ini
        // =====================
        $thisMonth = [
            'total_orders'  => Order::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),
            'total_revenue' => Order::where('status', 'completed')
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->sum('final_amount'),
            'avg_per_day'   => Order::where('status', 'completed')
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->sum('final_amount') / now()->day,
        ];

        // =====================
        // Grafik 7 Hari Terakhir
        // =====================
        $last7Days = collect(range(6, 0))->map(function($days) {
            $date = Carbon::today()->subDays($days);
            return [
                'date'    => $date->format('d/m'),
                'revenue' => Order::where('status', 'completed')
                                  ->whereDate('created_at', $date)
                                  ->sum('final_amount'),
                'orders'  => Order::whereDate('created_at', $date)->count(),
            ];
        });

        $chartLabels  = $last7Days->pluck('date');
        $chartRevenue = $last7Days->pluck('revenue');
        $chartOrders  = $last7Days->pluck('orders');

        // =====================
        // Menu Terlaris
        // =====================
        $topMenus = OrderItem::select('menu_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                             ->with('menu')
                             ->whereHas('order', fn($q) => $q->where('status', 'completed'))
                             ->groupBy('menu_id')
                             ->orderByDesc('total_qty')
                             ->limit(5)
                             ->get();

        // =====================
        // Stok Menipis
        // =====================
        $lowStocks = Ingredient::whereRaw('stock_quantity <= min_stock')
                               ->orderBy('stock_quantity')
                               ->limit(5)
                               ->get();

        // =====================
        // Shift Aktif Hari Ini
        // =====================
        $activeShifts = Shift::with('user')
                             ->whereDate('start_time', today())
                             ->orderBy('start_time')
                             ->get();

        // =====================
        // Total User per Role
        // =====================
        $userStats = User::select('role', DB::raw('count(*) as total'))
                         ->where('is_active', true)
                         ->groupBy('role')
                         ->pluck('total', 'role');

        return view('admin.dashboard', compact(
            'today',
            'thisMonth',
            'chartLabels',
            'chartRevenue',
            'chartOrders',
            'topMenus',
            'lowStocks',
            'activeShifts',
            'userStats'
        ));
    }
}