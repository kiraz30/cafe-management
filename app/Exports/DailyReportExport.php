<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class DailyReportExport
{
    protected $startDate;
    protected $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();

        $this->buildSummarySheet($spreadsheet->getActiveSheet());

        $sheet2 = $spreadsheet->createSheet();
        $this->buildDetailSheet($sheet2);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildSummarySheet($sheet)
    {
        $sheet->setTitle('Ringkasan');

        $orders    = Order::with(['payment'])
                          ->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate])
                          ->get();
        $completed = $orders->where('status', 'completed');

        $totalRevenue = $completed->sum('final_amount');
        $totalCash    = $completed->filter(fn($o) => $o->payment?->payment_method === 'cash')->sum('final_amount');
        $totalQris    = $completed->filter(fn($o) => $o->payment?->payment_method === 'qris')->sum('final_amount');

        // Judul
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN');
        $sheet->setCellValue('A2', 'Periode: ' . Carbon::parse($this->startDate)->isoFormat('D MMMM Y') . ' s/d ' . Carbon::parse($this->endDate)->isoFormat('D MMMM Y'));
        $sheet->setCellValue('A3', 'Dicetak: ' . now()->format('d/m/Y H:i'));

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2C3E50']],
        ]);
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '666666']],
        ]);

        // Ringkasan Order
        $sheet->setCellValue('A5', 'RINGKASAN ORDER');
        $this->styleSectionHeader($sheet, 'A5:B5');

        $orderData = [
            ['Total Order',       $orders->count()],
            ['Order Selesai',     $completed->count()],
            ['Order Dibatalkan',  $orders->where('status', 'cancelled')->count()],
            ['Order Pending',     $orders->where('status', 'pending')->count()],
            ['Dine In',           $orders->where('order_type', 'dine_in')->count()],
            ['Take Away',         $orders->where('order_type', 'take_away')->count()],
        ];

        $row = 6;
        foreach ($orderData as $i => $data) {
            $sheet->setCellValue("A{$row}", $data[0]);
            $sheet->setCellValue("B{$row}", $data[1]);
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                ]);
            }
            $row++;
        }

        // Ringkasan Pendapatan
        $row++;
        $sheet->setCellValue("A{$row}", 'RINGKASAN PENDAPATAN');
        $this->styleSectionHeader($sheet, "A{$row}:B{$row}");
        $row++;

        $revenueData = [
            ['Total Pendapatan',         $totalRevenue],
            ['Pendapatan Tunai (Cash)',   $totalCash],
            ['Pendapatan QRIS',           $totalQris],
        ];

        foreach ($revenueData as $i => $data) {
            $sheet->setCellValue("A{$row}", $data[0]);
            $sheet->setCellValue("B{$row}", $data[1]);
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle("B{$row}")->getFont()->setBold(true);
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                ]);
            }
            $row++;
        }

        // Top Menu
        $row++;
        $sheet->setCellValue("A{$row}", 'MENU TERLARIS');
        $this->styleSectionHeader($sheet, "A{$row}:C{$row}");
        $row++;

        $sheet->setCellValue("A{$row}", 'Menu');
        $sheet->setCellValue("B{$row}", 'Qty Terjual');
        $sheet->setCellValue("C{$row}", 'Total Pendapatan');
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECF0F1']],
        ]);
        $row++;

        $topMenus = OrderItem::select('menu_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                             ->with('menu')
                             ->whereHas('order', fn($q) => $q->where('status', 'completed')
                                                             ->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate]))
                             ->groupBy('menu_id')
                             ->orderByDesc('total_qty')
                             ->limit(5)
                             ->get();

        foreach ($topMenus as $i => $item) {
            $sheet->setCellValue("A{$row}", $item->menu->name ?? '-');
            $sheet->setCellValue("B{$row}", $item->total_qty);
            $sheet->setCellValue("C{$row}", $item->total_revenue);
            $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                ]);
            }
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(25);
    }

    private function buildDetailSheet($sheet)
    {
        $sheet->setTitle('Detail Transaksi');

        $headers = [
            'Kode Order', 'Tanggal', 'Waktu', 'Kasir', 'Tipe', 'Meja',
            'Item Pesanan', 'Subtotal', 'Pajak', 'Diskon',
            'Total', 'Metode Bayar', 'Jumlah Bayar', 'Kembalian', 'Status',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue("{$col}1", $header);
            $col++;
        }

        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $orders = Order::with(['user', 'table', 'payment', 'items.menu'])
                       ->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate])
                       ->orderBy('created_at')
                       ->get();

        $row = 2;
        foreach ($orders as $i => $order) {
            $rowData = [
                $order->order_code,
                $order->created_at->format('d/m/Y'),
                $order->created_at->format('H:i'),
                $order->user->name,
                $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away',
                $order->table ? 'Meja ' . $order->table->table_number : '-',
                $order->items->map(fn($item) => $item->menu->name . ' x' . $item->quantity)->implode(', '),
                $order->total_amount,
                $order->tax_amount,
                $order->discount_amount,
                $order->final_amount,
                $order->payment ? ucfirst($order->payment->payment_method) : '-',
                $order->payment ? $order->payment->amount_paid : 0,
                $order->payment ? ($order->payment->amount_paid - $order->final_amount) : 0,
                match($order->status) {
                    'completed'  => 'Selesai',
                    'cancelled'  => 'Dibatalkan',
                    'pending'    => 'Pending',
                    'processing' => 'Diproses',
                    default      => $order->status,
                },
            ];

            $col = 'A';
            foreach ($rowData as $value) {
                $sheet->setCellValue("{$col}{$row}", $value);
                $col++;
            }

            foreach (['H', 'I', 'J', 'K', 'M', 'N'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            }

            if ($i % 2 === 0) {
                $sheet->getStyle("A{$row}:O{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                ]);
            }

            $row++;
        }

        $lastRow = $row - 1;
        if ($lastRow >= 1) {
            $sheet->getStyle("A1:O{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E0E0E0'],
                    ],
                ],
            ]);
        }

        $widths = ['A' => 22, 'B' => 12, 'C' => 8, 'D' => 18, 'E' => 12,
                   'F' => 12, 'G' => 45, 'H' => 15, 'I' => 12, 'J' => 12,
                   'K' => 15, 'L' => 14, 'M' => 15, 'N' => 12, 'O' => 12];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }
    }

    private function styleSectionHeader($sheet, $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
        ]);
    }
}