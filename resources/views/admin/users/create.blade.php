@extends('layouts.app')

@section('page-title', 'Tambah User')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="page-header">
    <h5>Tambah User Baru</h5>
    <p>Buat akun pengguna baru</p>
</div>

<div class="card">
    <div class="card-header">
        <span style="font-size: 14px; font-weight: 600;">Form Tambah User</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           placeholder="Nama lengkap">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="email@contoh.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 6 karakter">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                           class="form-control"
                           placeholder="Ulangi password">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin"   {{ old('role') == 'admin'   ? 'selected' : '' }}>Admin</option>
                        <option value="kasir"   {{ old('role') == 'kasir'   ? 'selected' : '' }}>Kasir</option>
                        <option value="barista" {{ old('role') == 'barista' ? 'selected' : '' }}>Barista</option>
                        <option value="pelayan" {{ old('role') == 'pelayan' ? 'selected' : '' }}>Pelayan</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_active" id="is_active"
                               class="form-check-input" checked>
                        <label for="is_active" class="form-check-label" style="font-size: 14px;">
                            Aktif
                        </label>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark px-4">
                    Simpan User
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection