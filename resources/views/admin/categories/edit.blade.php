@extends('layouts.app')
@section('page-title', 'Edit Kategori')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="page-header">
    <h5>Edit Kategori</h5>
    <p>Ubah data kategori</p>
</div>

<div class="card">
    <div class="card-header">
        <span style="font-size: 14px; font-weight: 600;">Form Edit Kategori</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $category->name) }}">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_active" id="is_active"
                               class="form-check-input"
                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label" style="font-size: 14px;">Aktif</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark px-4">Update</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection