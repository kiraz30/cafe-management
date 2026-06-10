@extends('layouts.app')
@section('page-title', 'Tambah Meja')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="page-header">
    <h5>Tambah Meja Baru</h5>
    <p>Tambah meja baru ke denah cafe</p>
</div>

<div class="card" style="max-width: 500px;">
    <div class="card-header">
        <span style="font-size: 14px; font-weight: 600;">Form Tambah Meja</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.tables.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nomor Meja</label>
                <input type="text" name="table_number"
                       class="form-control @error('table_number') is-invalid @enderror"
                       value="{{ old('table_number') }}"
                       placeholder="Contoh: 01, A1, VIP-1">
                @error('table_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Kapasitas (orang)</label>
                <input type="number" name="capacity"
                       class="form-control @error('capacity') is-invalid @enderror"
                       value="{{ old('capacity', 2) }}" min="1" max="20">
                @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label">Status Awal</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                    <option value="occupied"  {{ old('status') == 'occupied'  ? 'selected' : '' }}>Terisi</option>
                    <option value="reserved"  {{ old('status') == 'reserved'  ? 'selected' : '' }}>Reservasi</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <hr class="my-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark px-4">Simpan Meja</button>
                <a href="{{ route('admin.tables.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection