@extends('layouts.app')
@section('page-title', 'Edit Bahan Baku')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="page-header">
    <h5>Edit Bahan Baku</h5>
    <p>Ubah data bahan baku</p>
</div>

<div class="card">
    <div class="card-header">
        <span style="font-size: 14px; font-weight: 600;">Form Edit Bahan Baku</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.ingredients.update', $ingredient) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Bahan</label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $ingredient->name) }}">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Satuan</label>
                    <select name="unit" class="form-select @error('unit') is-invalid @enderror">
                        @foreach(['kg','g','liter','ml','pcs','box','pack','botol','sachet'] as $unit)
                        <option value="{{ $unit }}" {{ old('unit', $ingredient->unit) == $unit ? 'selected' : '' }}>
                            {{ strtoupper($unit) }}
                        </option>
                        @endforeach
                    </select>
                    @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        Stok Saat Ini
                        <span style="font-size: 11px; color: #888; font-weight: 400;">
                            (ubah via Adjust Stok)
                        </span>
                    </label>
                    <input type="number" class="form-control"
                           value="{{ $ingredient->stock_quantity }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minimum Stok</label>
                    <input type="number" name="min_stock"
                           class="form-control @error('min_stock') is-invalid @enderror"
                           value="{{ old('min_stock', $ingredient->min_stock) }}" min="0" step="0.01">
                    @error('min_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga per Satuan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="cost_per_unit"
                               class="form-control @error('cost_per_unit') is-invalid @enderror"
                               value="{{ old('cost_per_unit', $ingredient->cost_per_unit) }}" min="0" step="100">
                    </div>
                    @error('cost_per_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark px-4">Update</button>
                <a href="{{ route('admin.ingredients.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection