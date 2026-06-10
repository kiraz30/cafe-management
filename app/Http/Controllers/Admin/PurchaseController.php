<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'user'])
                             ->orderBy('created_at', 'desc')
                             ->paginate(10);
        return view('admin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers   = Supplier::where('is_active', true)->orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        return view('admin.purchases.create', compact('suppliers', 'ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'purchase_date'        => 'required|date',
            'items'                => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ], [
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'items.required'       => 'Minimal 1 item pembelian.',
        ]);

        DB::transaction(function () use ($request) {
            // Generate kode pembelian
            $code = 'PO-' . date('Ymd') . '-' . str_pad(Purchase::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

            // Hitung total
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['quantity'] * $item['unit_price'];
            }

            $purchase = Purchase::create([
                'supplier_id'   => $request->supplier_id,
                'user_id'       => Auth::id(),
                'purchase_code' => $code,
                'total_amount'  => $total,
                'status'        => $request->status ?? 'received',
                'purchase_date' => $request->purchase_date,
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];

                PurchaseItem::create([
                    'purchase_id'   => $purchase->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['unit_price'],
                    'subtotal'      => $subtotal,
                ]);

                // Update stok jika status received
                if ($purchase->status === 'received') {
                    $ingredient = Ingredient::find($item['ingredient_id']);
                    $ingredient->increment('stock_quantity', $item['quantity']);

                    StockMovement::create([
                        'ingredient_id'  => $item['ingredient_id'],
                        'type'           => 'in',
                        'quantity'       => $item['quantity'],
                        'reference_id'   => $purchase->id,
                        'reference_type' => 'purchase',
                        'notes'          => 'Pembelian ' . $purchase->purchase_code,
                    ]);
                }
            }
        });

        return redirect()->route('admin.purchases.index')
                         ->with('success', 'Pembelian berhasil disimpan.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.ingredient']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->route('admin.purchases.index')
                         ->with('success', 'Data pembelian berhasil dihapus.');
    }
}