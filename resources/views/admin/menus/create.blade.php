@extends('layouts.app')
@section('page-title', 'Tambah Menu')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="page-header">
    <h5>Tambah Menu Baru</h5>
    <p>Tambah menu baru ke daftar</p>
</div>

<div class="card">
    <div class="card-header">
        <span style="font-size: 14px; font-weight: 600;">Form Tambah Menu</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.menus.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Menu</label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           placeholder="Contoh: Cappuccino">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Harga</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="price"
                               class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', 0) }}" min="0" step="500">
                    </div>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gambar Menu</label>
                    <input type="file" name="image"
                           class="form-control @error('image') is-invalid @enderror"
                           accept="image/*"
                           onchange="previewImage(this)">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="mt-2" id="imagePreview" style="display:none;">
                        <img id="previewImg" src=""
                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e0e0e0;">
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="Deskripsi menu (opsional)">{{ old('description') }}</textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_available" id="is_available"
                               class="form-check-input" checked>
                        <label for="is_available" class="form-check-label" style="font-size: 14px;">
                            Tersedia
                        </label>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark px-4">Simpan Menu</button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection