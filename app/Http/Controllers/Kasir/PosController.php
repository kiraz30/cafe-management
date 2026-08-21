<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\MidtransService;

class PosController extends Controller
{
    // Tampilan utama POS
    public function index()
    {
        $categories = Category::where('is_active', true)
                               ->withCount(['menus' => function($q) {
                                   $q->where('is_available', true);
                               }])
                               ->get();

        $menus = Menu::with('category')
                     ->where('is_available', true)
                     ->orderBy('category_id')
                     ->orderBy('name')
                     ->get();

        $tables = Table::where('status', 'available')
                       ->orderBy('table_number')
                       ->get();

        $settings = [
            'payment_cash' => Setting::get('payment_cash', '1'),
            'payment_qris' => Setting::get('payment_qris', '1'),
            'qris_type'    => Setting::get('qris_type', 'static'),
            'qris_image'   => Setting::get('qris_image', ''),
            'tax'          => Setting::get('tax_percentage', '0'),
            'discount'     => Setting::get('discount_percentage', '0'),
        ];

        return view('kasir.pos.index', compact(
            'categories', 'menus', 'tables', 'settings'
        ));
    }

    // Buat order baru
    public function store(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:dine_in,take_away',
            'table_id'   => 'nullable|exists:tables,id',
            'items'      => 'required|array|min:1',
            'items.*.menu_id'  => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes'    => 'nullable|string',
            'notes'      => 'nullable|string',
        ], [
            'items.required' => 'Minimal 1 item pesanan.',
            'order_type.required' => 'Tipe order wajib dipilih.',
        ]);

        // Validasi meja jika dine_in
        // if ($request->order_type === 'dine_in' && !$request->table_id) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Pilih meja untuk dine-in.'
        //     ], 422);
        // }

        DB::beginTransaction();
        try {
            // Generate order code
            $prefix    = Setting::get('order_prefix', 'ORD');
            $count     = Order::whereDate('created_at', today())->count() + 1;
            $orderCode = $prefix . '-' . date('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            // Hitung total
            $totalAmount = 0;
            $orderItems  = [];

            foreach ($request->items as $item) {
                $menu      = Menu::findOrFail($item['menu_id']);
                $subtotal  = $menu->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'menu_id'    => $menu->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $menu->price,
                    'subtotal'   => $subtotal,
                    'notes'      => $item['notes'] ?? null,
                ];
            }

            // Hitung pajak & diskon
            $taxPercent      = (float) Setting::get('tax_percentage', 0);
            $discountPercent = (float) Setting::get('discount_percentage', 0);
            $taxAmount       = $totalAmount * ($taxPercent / 100);
            $discountAmount  = $totalAmount * ($discountPercent / 100);
            $finalAmount     = $totalAmount + $taxAmount - $discountAmount;

            // Buat order
            $order = Order::create([
                'user_id'         => Auth::id(),
                'table_id'        => $request->table_id,
                'order_code'      => $orderCode,
                'status'          => 'pending',
                'order_type'      => $request->order_type,
                'total_amount'    => $totalAmount,
                'tax_amount'      => $taxAmount,
                'discount_amount' => $discountAmount,
                'final_amount'    => $finalAmount,
                'notes'           => $request->notes,
            ]);

            // Buat order items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            // Update status meja jika dine_in
            if ($request->order_type === 'dine_in' && $request->table_id) {
                Table::find($request->table_id)->update(['status' => 'occupied']);
            }

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Order berhasil dibuat.',
                'order_id'   => $order->id,
                'order_code' => $order->order_code,
                'final_amount' => $order->final_amount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat order: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Proses pembayaran
    public function payment(Request $request, Order $order)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,qris',
            'amount_paid'    => 'required|numeric|min:0',
        ], [
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'amount_paid.required'    => 'Jumlah bayar wajib diisi.',
        ]);

        // Validasi uang tunai mencukupi
        if ($request->payment_method === 'cash' && $request->amount_paid < $order->final_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Uang yang diterima kurang dari total tagihan.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Buat payment
            Payment::create([
                'order_id'         => $order->id,
                'amount_paid'      => $request->amount_paid,
                'payment_method'   => $request->payment_method,
                'status'           => 'paid',
                'reference_number' => $request->reference_number ?? null,
            ]);

            // Status order tetap "pending" agar barista bisa proses
            // Hanya update jika belum ada status processing/completed
            if ($order->status === 'pending') {
                $order->update(['status' => 'pending']);
            }

            // Bebaskan meja jika dine_in — JANGAN dulu, tunggu barista selesai
            // Table akan dibebaskan saat barista update status ke completed

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Pembayaran berhasil. Pesanan diteruskan ke dapur.',
                'order_id'   => $order->id,
                'change'     => $request->amount_paid - $order->final_amount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Ambil detail order untuk struk
    public function receipt(Order $order)
    {
        $order->load(['items.menu', 'payment', 'table', 'user']);

        $settings = [
            'cafe_name'      => Setting::get('cafe_name', 'Cafe Management'),
            'cafe_address'   => Setting::get('cafe_address', ''),
            'cafe_phone'     => Setting::get('cafe_phone', ''),
            'cafe_logo'      => Setting::get('cafe_logo', ''),
            'receipt_header' => Setting::get('receipt_header', ''),
            'receipt_footer' => Setting::get('receipt_footer', ''),
            'receipt_show_logo' => Setting::get('receipt_show_logo', '1'),
        ];

        return view('kasir.pos.receipt', compact('order', 'settings'));
    }

    // Batalkan order
    public function cancel(Order $order)
    {
        if (!$order->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Order yang sudah selesai tidak bisa dibatalkan.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Bebaskan meja
            if ($order->table_id) {
                Table::find($order->table_id)->update(['status' => 'available']);
            }

            $order->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibatalkan.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan order.',
            ], 500);
        }
    }

    // Ambil data semua meja untuk panel POS
    public function tables()
    {
        $tables = Table::orderBy('table_number')->get();
        return response()->json([
            'success' => true,
            'tables'  => $tables,
        ]);
    }

    // Update status meja dari POS
    public function updateTable(Request $request, Table $table)
    {
        $request->validate([
            'status'         => 'required|in:available,occupied,reserved',
            'reserved_name'  => 'nullable|string|max:100',
            'reserved_time'  => 'nullable|string|max:50',
            'reserved_notes' => 'nullable|string',
        ], [
            'status.required' => 'Status wajib dipilih.',
        ]);

        // Validasi jika reserved harus ada nama
        if ($request->status === 'reserved' && !$request->reserved_name) {
            return response()->json([
                'success' => false,
                'message' => 'Nama pemesan wajib diisi untuk reservasi.',
            ], 422);
        }

        $data = ['status' => $request->status];

        // Jika reserved, simpan info reservasi
        if ($request->status === 'reserved') {
            $data['reserved_name']  = $request->reserved_name;
            $data['reserved_time']  = $request->reserved_time;
            $data['reserved_notes'] = $request->reserved_notes;
        } else {
            // Jika bukan reserved, kosongkan info reservasi
            $data['reserved_name']  = null;
            $data['reserved_time']  = null;
            $data['reserved_notes'] = null;
        }

        $table->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Status meja berhasil diupdate.',
            'table'   => $table->fresh(),
        ]);
    }

    //kitchen
    public function kitchenReceipt(Order $order)
    {
        $order->load(['items.menu', 'table', 'user']);

        $settings = [
            'cafe_name' => Setting::get('cafe_name', 'Cafe Management'),
        ];

        return view('kasir.pos.kitchen-receipt', compact('order', 'settings'));
    }

    // Buat QRIS dinamis
    public function createQris(Request $request, Order $order)
    {
        try {
            $midtrans = new MidtransService();
            $result   = $midtrans->createQrisTransaction([
                'order_code'   => $order->order_code,
                'final_amount' => $order->final_amount,
            ]);

            return response()->json([
                'success'     => true,
                'qr_code_url' => $result['qr_code_url'],
                'order_id'    => $result['order_id'],
                'expire_time' => $result['expire_time'],
                'qr_string'   => $result['qr_string'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat QRIS: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Cek status pembayaran QRIS
    public function checkQrisStatus(Request $request, Order $order)
    {
        try {
            $midtransOrderId = $request->midtrans_order_id;
            $midtrans        = new MidtransService();
            $status          = $midtrans->checkStatus($midtransOrderId);

            $paid = in_array($status, ['settlement', 'capture']);

            if ($paid) {
                // Proses pembayaran
                DB::beginTransaction();
                try {
                    Payment::create([
                        'order_id'         => $order->id,
                        'amount_paid'      => $order->final_amount,
                        'payment_method'   => 'qris',
                        'status'           => 'paid',
                        'reference_number' => $midtransOrderId,
                    ]);

                    $order->update(['status' => 'pending']);

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            return response()->json([
                'success' => true,
                'paid'    => $paid,
                'status'  => $status,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}