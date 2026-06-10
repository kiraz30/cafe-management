@extends('layouts.app')
@section('page-title', 'Tambah Bahan Baku')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="page-header">
    <h5>Tambah Bahan Baku</h5>
    <p>Tambah bahan baku baru ke sistem</p>
</div>

<div class="card">
    <div class="card-header">
        <span style="font-size: 14px; font-weight: 600;">Form Tambah Bahan Baku</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.ingredients.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Bahan</label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Contoh: Kopi Arabica">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Satuan</label>
                    <select name="unit" class="form-select @error('unit') is-invalid @enderror">
                        <option value="">-- Pilih Satuan --</option>
                        <option value="kg"   {{ old('unit') == 'kg'   ? 'selected' : '' }}>Kilogram (kg)</option>
                        <option value="g"    {{ old('unit') == 'g'    ? 'selected' : '' }}>Gram (g)</option>
                        <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                        <option value="ml"   {{ old('unit') == 'ml'   ? 'selected' : '' }}>Mililiter (ml)</option>
                        <option value="pcs"  {{ old('unit') == 'pcs'  ? 'selected' : '' }}>Pcs</option>
                        <option value="box"  {{ old('unit') == 'box'  ? 'selected' : '' }}>Box</option>
                        <option value="pack" {{ old('unit') == 'pack' ? 'selected' : '' }}>Pack</option>
                        <option value="botol" {{ old('unit') == 'botol' ? 'selected' : '' }}>Botol</option>
                        <option value="sachet" {{ old('unit') == 'sachet' ? 'selected' : '' }}>Sachet</option>
                    </select>
                    @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stok Awal</label>
                    <div class="input-group">
                        <input type="number" name="stock_quantity"
                               class="form-control @error('stock_quantity') is-invalid @enderror"
                               value="{{ old('stock_quantity', 0) }}" min="0" step="0.01">
                    </div>
                    @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minimum Stok</label>
                    <input type="number" name="min_stock"
                           class="form-control @error('min_stock') is-invalid @enderror"
                           value="{{ old('min_stock', 0) }}" min="0" step="0.01">
                    @error('min_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga per Satuan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="cost_per_unit"
                               class="form-control @error('cost_per_unit') is-invalid @enderror"
                               value="{{ old('cost_per_unit', 0) }}" min="0" step="100">
                    </div>
                    @error('cost_per_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark px-4">Simpan</button>
                <a href="{{ route('admin.ingredients.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection