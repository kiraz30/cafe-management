<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Exports\DailyReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default: hari ini
        $startDate = $request->start_date ?? today()->toDateString();
        $endDate   = $request->end_date   ?? today()->toDateString();
        $period    = $request->period     ?? 'today';

        // Handle filter cepat
        switch ($period) {
            case 'today':
                $startDate = today()->toDateString();
                $endDate   = today()->toDateString();
                break;
            case 'last7days':
                $startDate = today()->subDays(6)->toDateString();
                $endDate   = today()->toDateString();
                break;
            case 'this_month':
                $startDate = now()->startOfMonth()->toDateString();
                $endDate   = today()->toDateString();
                break;
            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth()->toDateString();
                $endDate   = now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'custom':
                $startDate = $request->start_date ?? today()->toDateString();
                $endDate   = $request->end_date   ?? today()->toDateString();
                break;
        }

        // Ambil data order
        $orders = Order::with(['user', 'table', 'payment', 'items.menu'])
                       ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
                       ->orderBy('created_at', 'desc')
                       ->get();

        // Summary
        $summary = [
            'total_orders'    => $orders->count(),
            'total_completed' => $orders->where('status', 'completed')->count(),
            'total_cancelled' => $orders->where('status', 'cancelled')->count(),
            'total_pending'   => $orders->where('status', 'pending')->count(),
            'total_revenue'   => $orders->where('status', 'completed')->sum('final_amount'),
            'total_cash'      => $orders->where('status', 'completed')
                                        ->filter(fn($o) => $o->payment?->payment_method === 'cash')
                                        ->sum('final_amount'),
            'total_qris'      => $orders->where('status', 'completed')
                                        ->filter(fn($o) => $o->payment?->payment_method === 'qris')
                                        ->sum('final_amount'),
            'total_dine_in'   => $orders->where('order_type', 'dine_in')->count(),
            'total_take_away' => $orders->where('order_type', 'take_away')->count(),
        ];

        // Top menu
        $topMenus = OrderItem::select('menu_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                             ->with('menu')
                             ->whereHas('order', fn($q) => $q->where('status', 'completed')
                                                             ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]))
                             ->groupBy('menu_id')
                             ->orderByDesc('total_qty')
                             ->limit(5)
                             ->get();

        // Label periode
        $periodLabel = $this->getPeriodLabel($period, $startDate, $endDate);

        return view('admin.reports.index', compact(
            'orders', 'summary', 'topMenus',
            'startDate', 'endDate', 'period', 'periodLabel'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->start_date ?? today()->toDateString();
        $endDate   = $request->end_date   ?? today()->toDateString();

        $filename = 'laporan-' . $startDate . '-sd-' . $endDate . '.xlsx';

        $export      = new DailyReportExport($startDate, $endDate);
        $spreadsheet = $export->export();
        $writer      = new Xlsx($spreadsheet);

        $tempFile = tempnam(sys_get_temp_dir(), 'report_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function getPeriodLabel($period, $startDate, $endDate): string
    {
        return match($period) {
            'today'      => 'Hari Ini — ' . Carbon::parse($startDate)->isoFormat('D MMMM Y'),
            'last7days'  => '7 Hari Terakhir — ' . Carbon::parse($startDate)->isoFormat('D MMM') . ' s/d ' . Carbon::parse($endDate)->isoFormat('D MMM Y'),
            'this_month' => 'Bulan Ini — ' . Carbon::parse($startDate)->isoFormat('MMMM Y'),
            'last_month' => 'Bulan Lalu — ' . Carbon::parse($startDate)->isoFormat('MMMM Y'),
            default      => Carbon::parse($startDate)->isoFormat('D MMM Y') . ' s/d ' . Carbon::parse($endDate)->isoFormat('D MMM Y'),
        };
    }
}