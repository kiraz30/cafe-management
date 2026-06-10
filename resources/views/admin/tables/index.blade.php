@extends('layouts.app')
@section('page-title', 'Manajemen Meja')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
    .table-card {
        border-radius: 12px;
        border: 2px solid #e0e0e0;
        padding: 20px;
        text-align: center;
        transition: all 0.2s;
        cursor: default;
        position: relative;
    }
    .table-card.available { border-color: #1e8449; background: #eafaf1; }
    .table-card.occupied  { border-color: #c0392b; background: #fdecea; }
    .table-card.reserved  { border-color: #b7770d; background: #fef9e7; }
    .table-icon { font-size: 36px; margin-bottom: 8px; }
    .table-number { font-size: 18px; font-weight: 700; color: #2c3e50; }
    .table-capacity { font-size: 12px; color: #888; margin-bottom: 12px; }
    .table-status { font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
    .status-available { background: #1e8449; color: #fff; }
    .status-occupied  { background: #c0392b; color: #fff; }
    .status-reserved  { background: #b7770d; color: #fff; }
    .table-actions { margin-top: 12px; display: flex; gap: 6px; justify-content: center; }
</style>

<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h5>Manajemen Meja</h5>
        <p>Kelola meja dan status ketersediaan</p>
    </div>
    <a href="{{ route('admin.tables.create') }}" class="btn btn-dark btn-sm px-3">
        + Tambah Meja
    </a>
</div>

{{-- Legenda --}}
<div class="d-flex gap-3 mb-4">
    <div class="d-flex align-items-center gap-2">
        <div style="width:14px;height:14px;border-radius:50%;background:#1e8449;"></div>
        <span style="font-size:13px;">Tersedia ({{ $tables->where('status','available')->count() }})</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div style="width:14px;height:14px;border-radius:50%;background:#c0392b;"></div>
        <span style="font-size:13px;">Terisi ({{ $tables->where('status','occupied')->count() }})</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div style="width:14px;height:14px;border-radius:50%;background:#b7770d;"></div>
        <span style="font-size:13px;">Direservasi ({{ $tables->where('status','reserved')->count() }})</span>
    </div>
</div>

{{-- Grid Meja --}}
<div class="row g-3">
    @forelse($tables as $table)
    <div class="col-6 col-md-3 col-lg-2">
        <div class="table-card {{ $table->status }}">
            <div class="table-icon">🪑</div>
            <div class="table-number">Meja {{ $table->table_number }}</div>
            <div class="table-capacity">👥 {{ $table->capacity }} orang</div>
            <span class="table-status status-{{ $table->status }}">
                {{ $table->status === 'available' ? 'Tersedia' : ($table->status === 'occupied' ? 'Terisi' : 'Reservasi') }}
            </span>

            {{-- Update Status --}}
            <div class="table-actions">
                <form method="POST" action="{{ route('admin.tables.update-status', $table) }}">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select form-select-sm"
                            onchange="this.form.submit()" style="font-size: 11px;">
                        <option value="available" {{ $table->status == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="occupied"  {{ $table->status == 'occupied'  ? 'selected' : '' }}>Terisi</option>
                        <option value="reserved"  {{ $table->status == 'reserved'  ? 'selected' : '' }}>Reservasi</option>
                    </select>
                </form>
            </div>

            {{-- Edit & Hapus --}}
            <div class="table-actions">
                <a href="{{ route('admin.tables.edit', $table) }}"
                   class="btn btn-outline-secondary btn-action">Edit</a>
                <form action="{{ route('admin.tables.destroy', $table) }}"
                      method="POST" class="d-inline"
                      onsubmit="return confirm('Yakin hapus meja ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-action">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5" style="color: #888;">
                Belum ada meja.
                <a href="{{ route('admin.tables.create') }}">Tambah sekarang</a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($tables->hasPages())
<div class="mt-4">{{ $tables->links() }}</div>
@endif

@endsection