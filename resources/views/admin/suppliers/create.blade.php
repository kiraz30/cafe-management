@extends('layouts.app')
@section('page-title', 'Tambah Supplier')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="page-header">
    <h5>Tambah Supplier</h5>
    <p>Tambah data supplier baru</p>
</div>

<div class="card">
    <div class="card-header">
        <span style="font-size: 14px; font-weight: 600;">Form Tambah Supplier</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.suppliers.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Supplier</label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Nama supplier">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person"
                           class="form-control"
                           value="{{ old('contact_person') }}" placeholder="Nama kontak">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone"
                           class="form-control"
                           value="{{ old('phone') }}" placeholder="08xx-xxxx-xxxx">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="email@supplier.com">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="3"
                              placeholder="Alamat lengkap supplier">{{ old('address') }}</textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active"
                               class="form-check-input" checked>
                        <label for="is_active" class="form-check-label" style="font-size: 14px;">Aktif</label>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark px-4">Simpan</button>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection