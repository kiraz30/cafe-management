@extends('layouts.app')
@section('page-title', 'Edit Meja')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="page-header">
    <h5>Edit Meja</h5>
    <p>Ubah data meja {{ $table->table_number }}</p>
</div>

<div class="card" style="max-width: 500px;">
    <div class="card-header">
        <span style="font-size: 14px; font-weight: 600;">Form Edit Meja</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.tables.update', $table) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nomor Meja</label>
                <input type="text" name="table_number"
                       class="form-control @error('table_number') is-invalid @enderror"
                       value="{{ old('table_number', $table->table_number) }}">
                @error('table_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Kapasitas (orang)</label>
                <input type="number" name="capacity"
                       class="form-control @error('capacity') is-invalid @enderror"
                       value="{{ old('capacity', $table->capacity) }}" min="1" max="20">
                @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="available" {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>Tersedia</option>
                    <option value="occupied"  {{ old('status', $table->status) == 'occupied'  ? 'selected' : '' }}>Terisi</option>
                    <option value="reserved"  {{ old('status', $table->status) == 'reserved'  ? 'selected' : '' }}>Reservasi</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <hr class="my-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark px-4">Update Meja</button>
                <a href="{{ route('admin.tables.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection