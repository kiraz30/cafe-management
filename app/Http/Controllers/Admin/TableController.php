<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::orderBy('table_number')->paginate(12);
        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|max:10|unique:tables,table_number',
            'capacity'     => 'required|integer|min:1|max:20',
            'status'       => 'required|in:available,occupied,reserved',
        ], [
            'table_number.required' => 'Nomor meja wajib diisi.',
            'table_number.unique'   => 'Nomor meja sudah ada.',
            'capacity.required'     => 'Kapasitas wajib diisi.',
            'status.required'       => 'Status wajib dipilih.',
        ]);

        Table::create($request->all());

        return redirect()->route('admin.tables.index')
                         ->with('success', 'Meja berhasil ditambahkan.');
    }

    public function edit(Table $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'table_number' => 'required|string|max:10|unique:tables,table_number,' . $table->id,
            'capacity'     => 'required|integer|min:1|max:20',
            'status'       => 'required|in:available,occupied,reserved',
        ], [
            'table_number.required' => 'Nomor meja wajib diisi.',
            'table_number.unique'   => 'Nomor meja sudah ada.',
            'capacity.required'     => 'Kapasitas wajib diisi.',
        ]);

        $table->update($request->all());

        return redirect()->route('admin.tables.index')
                         ->with('success', 'Meja berhasil diupdate.');
    }

    public function destroy(Table $table)
    {
        if ($table->isOccupied()) {
            return redirect()->route('admin.tables.index')
                             ->with('error', 'Meja tidak bisa dihapus karena sedang digunakan.');
        }

        $table->delete();
        return redirect()->route('admin.tables.index')
                         ->with('success', 'Meja berhasil dihapus.');
    }

    // Update status meja langsung dari tampilan
    public function updateStatus(Request $request, Table $table)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved',
        ]);

        $table->update(['status' => $request->status]);

        return back()->with('success', 'Status meja berhasil diupdate.');
    }
}