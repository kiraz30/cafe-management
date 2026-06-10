<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with('user')
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);
        return view('admin.shifts.index', compact('shifts'));
    }

    public function create()
    {
        $users = User::where('is_active', true)
                     ->orderBy('name')
                     ->get();
        return view('admin.shifts.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'start_time'   => 'required|date',
            'end_time'     => 'nullable|date|after:start_time',
            'opening_cash' => 'required|numeric|min:0',
            'closing_cash' => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ], [
            'user_id.required'      => 'Karyawan wajib dipilih.',
            'user_id.exists'        => 'Karyawan tidak ditemukan.',
            'start_time.required'   => 'Waktu mulai wajib diisi.',
            'end_time.after'        => 'Waktu selesai harus setelah waktu mulai.',
            'opening_cash.required' => 'Kas awal wajib diisi.',
            'opening_cash.numeric'  => 'Kas awal harus berupa angka.',
        ]);

        Shift::create($request->all());

        return redirect()->route('admin.shifts.index')
                         ->with('success', 'Shift berhasil ditambahkan.');
    }

    public function show(Shift $shift)
    {
        $shift->load('user');
        $users = User::where('is_active', true)
                ->orderBy('name')
                ->get();
        return view('admin.shifts.show', compact('shift','users'));
    }

    public function edit(Shift $shift)
    {
        $users = User::where('is_active', true)
                     ->orderBy('name')
                     ->get();
        return view('admin.shifts.edit', compact('shift', 'users'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'start_time'   => 'required|date',
            'end_time'     => 'nullable|date|after:start_time',
            'opening_cash' => 'required|numeric|min:0',
            'closing_cash' => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ], [
            'user_id.required'      => 'Karyawan wajib dipilih.',
            'start_time.required'   => 'Waktu mulai wajib diisi.',
            'end_time.after'        => 'Waktu selesai harus setelah waktu mulai.',
            'opening_cash.required' => 'Kas awal wajib diisi.',
        ]);

        $shift->update($request->all());

        return redirect()->route('admin.shifts.index')
                         ->with('success', 'Shift berhasil diupdate.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();

        return redirect()->route('admin.shifts.index')
                         ->with('success', 'Shift berhasil dihapus.');
    }
}