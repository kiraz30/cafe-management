<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::orderBy('name')->paginate(10);
        return view('admin.ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('admin.ingredients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'unit'          => 'required|string|max:50',
            'stock_quantity' => 'required|numeric|min:0',
            'min_stock'     => 'required|numeric|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
        ], [
            'name.required'          => 'Nama bahan wajib diisi.',
            'unit.required'          => 'Satuan wajib diisi.',
            'stock_quantity.required' => 'Stok awal wajib diisi.',
            'min_stock.required'     => 'Minimum stok wajib diisi.',
            'cost_per_unit.required' => 'Harga per satuan wajib diisi.',
        ]);

        $ingredient = Ingredient::create($request->all());

        // Catat stock movement awal
        if ($request->stock_quantity > 0) {
            StockMovement::create([
                'ingredient_id'  => $ingredient->id,
                'type'           => 'in',
                'quantity'       => $request->stock_quantity,
                'reference_type' => 'initial',
                'notes'          => 'Stok awal',
            ]);
        }

        return redirect()->route('admin.ingredients.index')
                         ->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function show(Ingredient $ingredient)
    {
        $movements = $ingredient->stockMovements()
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);
        return view('admin.ingredients.show', compact('ingredient', 'movements'));
    }

    public function edit(Ingredient $ingredient)
    {
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'unit'          => 'required|string|max:50',
            'min_stock'     => 'required|numeric|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
        ]);

        $ingredient->update([
            'name'          => $request->name,
            'unit'          => $request->unit,
            'min_stock'     => $request->min_stock,
            'cost_per_unit' => $request->cost_per_unit,
        ]);

        return redirect()->route('admin.ingredients.index')
                         ->with('success', 'Bahan baku berhasil diupdate.');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return redirect()->route('admin.ingredients.index')
                         ->with('success', 'Bahan baku berhasil dihapus.');
    }

    // Tambah/kurang stok manual
    public function adjustStock(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'type'     => 'required|in:in,out,adjustment',
            'quantity' => 'required|numeric|min:0.01',
            'notes'    => 'nullable|string',
        ]);

        if ($request->type === 'out' && $ingredient->stock_quantity < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $newStock = match($request->type) {
            'in'         => $ingredient->stock_quantity + $request->quantity,
            'out'        => $ingredient->stock_quantity - $request->quantity,
            'adjustment' => $request->quantity,
        };

        $ingredient->update(['stock_quantity' => $newStock]);

        StockMovement::create([
            'ingredient_id'  => $ingredient->id,
            'type'           => $request->type,
            'quantity'       => $request->quantity,
            'reference_type' => 'manual',
            'notes'          => $request->notes,
        ]);

        return back()->with('success', 'Stok berhasil diupdate.');
    }
}