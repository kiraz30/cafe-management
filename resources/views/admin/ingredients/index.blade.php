@extends('layouts.app')
@section('page-title', 'Bahan Baku')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h5>Manajemen Bahan Baku</h5>
        <p>Kelola stok bahan baku cafe</p>
    </div>
    <a href="{{ route('admin.ingredients.create') }}" class="btn btn-dark btn-sm px-3">
        + Tambah Bahan
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span style="font-size: 14px; font-weight: 600;">Daftar Bahan Baku</span>
        <span style="font-size: 12px; color: #888;">Total: {{ $ingredients->total() }} bahan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Bahan</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                        <th>Min. Stok</th>
                        <th>Harga/Satuan</th>
                        <th>Status Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ingredients as $ingredient)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><div style="font-weight: 600;">{{ $ingredient->name }}</div></td>
                        <td>{{ $ingredient->unit }}</td>
                        <td>{{ number_format($ingredient->stock_quantity, 2) }}</td>
                        <td>{{ number_format($ingredient->min_stock, 2) }}</td>
                        <td>Rp {{ number_format($ingredient->cost_per_unit, 0, ',', '.') }}</td>
                        <td>
                            @if($ingredient->isLowStock())
                                <span class="badge-inactive">⚠️ Menipis</span>
                            @else
                                <span class="badge-active">Aman</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.ingredients.show', $ingredient) }}"
                               class="btn btn-outline-info btn-action me-1">Detail</a>
                            <a href="{{ route('admin.ingredients.edit', $ingredient) }}"
                               class="btn btn-outline-secondary btn-action me-1">Edit</a>
                            <form action="{{ route('admin.ingredients.destroy', $ingredient) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus bahan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4" style="color: #888;">
                            Belum ada bahan baku.
                            <a href="{{ route('admin.ingredients.create') }}">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($ingredients->hasPages())
    <div class="card-footer bg-white py-3">{{ $ingredients->links() }}</div>
    @endif
</div>

@endsection