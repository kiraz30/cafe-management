@extends('layouts.app')
@section('page-title', 'Detail Bahan Baku')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h5>Detail Bahan Baku</h5>
        <p>{{ $ingredient->name }}</p>
    </div>
    <a href="{{ route('admin.ingredients.index') }}" class="btn btn-outline-secondary btn-sm">
        Kembali
    </a>
</div>

<div class="row g-4">
    {{-- Info Bahan --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <span style="font-size: 14px; font-weight: 600;">Info Bahan</span>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <div style="font-size: 12px; color: #888;">Nama Bahan</div>
                    <div style="font-weight: 600;">{{ $ingredient->name }}</div>
                </div>
                <div class="mb-3">
                    <div style="font-size: 12px; color: #888;">Satuan</div>
                    <div>{{ $ingredient->unit }}</div>
                </div>
                <div class="mb-3">
                    <div style="font-size: 12px; color: #888;">Stok Saat Ini</div>
                    <div class="{{ $ingredient->isLowStock() ? 'text-danger' : 'text-success' }}"
                        style="font-size: 20px; font-weight: 700;">
                        {{ number_format($ingredient->stock_quantity, 2) }} {{ $ingredient->unit }}
                    </div>
                </div>
                <div class="mb-3">
                    <div style="font-size: 12px; color: #888;">Minimum Stok</div>
                    <div>{{ number_format($ingredient->min_stock, 2) }} {{ $ingredient->unit }}</div>
                </div>
                <div class="mb-4">
                    <div style="font-size: 12px; color: #888;">Harga per Satuan</div>
                    <div>Rp {{ number_format($ingredient->cost_per_unit, 0, ',', '.') }}</div>
                </div>

                {{-- Form Adjust Stok --}}
                <hr>
                <div style="font-size: 13px; font-weight: 600; margin-bottom: 12px;">Adjust Stok</div>
                <form method="POST" action="{{ route('admin.ingredients.adjust-stock', $ingredient) }}">
                    @csrf
                    <div class="mb-2">
                        <select name="type" class="form-select form-select-sm">
                            <option value="in">+ Stok Masuk</option>
                            <option value="out">- Stok Keluar</option>
                            <option value="adjustment">🔄 Koreksi Stok</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <input type="number" name="quantity"
                               class="form-control form-control-sm"
                               placeholder="Jumlah" min="0.01" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="notes"
                               class="form-control form-control-sm"
                               placeholder="Catatan (opsional)">
                    </div>
                    <button type="submit" class="btn btn-dark btn-sm w-100">
                        Update Stok
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Riwayat Stok --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <span style="font-size: 14px; font-weight: 600;">Riwayat Pergerakan Stok</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Referensi</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $movement)
                            <tr>
                                <td style="font-size: 13px;">
                                    {{ $movement->created_at->format('d M Y, H:i') }}
                                </td>
                                <td>
                                    @if($movement->type === 'in')
                                        <span class="badge-active">Masuk</span>
                                    @elseif($movement->type === 'out')
                                        <span class="badge-inactive">Keluar</span>
                                    @else
                                        <span style="background:#e8f4fd;color:#1a6fa8;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;">Koreksi</span>
                                    @endif
                                </td>
                                <td>{{ number_format($movement->quantity, 2) }} {{ $ingredient->unit }}</td>
                                <td style="font-size: 13px; color: #888;">
                                    {{ $movement->reference_type ?? '-' }}
                                </td>
                                <td style="font-size: 13px; color: #888;">
                                    {{ $movement->notes ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4" style="color: #888;">
                                    Belum ada riwayat pergerakan stok.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($movements->hasPages())
            <div class="card-footer bg-white py-3">{{ $movements->links() }}</div>
            @endif
        </div>
    </div>
</div>

@endsection