@extends('layouts.app')

@section('page-title', 'Edit Shift')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="page-header">
    <h5>Edit Shift</h5>
    <p>Ubah data shift karyawan</p>
</div>

<div class="card">
    <div class="card-header">
        <span style="font-size: 14px; font-weight: 600;">Form Edit Shift</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.shifts.update', $shift) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Karyawan</label>
                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" 
                            {{ old('user_id', $shift->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ ucfirst($user->role) }})
                        </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kas Awal</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="opening_cash"
                               class="form-control @error('opening_cash') is-invalid @enderror"
                               value="{{ old('opening_cash', $shift->opening_cash) }}"
                               min="0" step="1000">
                    </div>
                    @error('opening_cash')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Waktu Mulai</label>
                    <input type="datetime-local" name="start_time"
                           class="form-control @error('start_time') is-invalid @enderror"
                           value="{{ old('start_time', $shift->start_time->format('Y-m-d\TH:i')) }}">
                    @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Waktu Selesai
                        <span style="font-size: 11px; color: #888; font-weight: 400;">
                            (kosongkan jika shift masih berjalan)
                        </span>
                    </label>
                    <input type="datetime-local" name="end_time"
                           class="form-control @error('end_time') is-invalid @enderror"
                           value="{{ old('end_time', $shift->end_time ? $shift->end_time->format('Y-m-d\TH:i') : '') }}">
                    @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Kas Akhir
                        <span style="font-size: 11px; color: #888; font-weight: 400;">
                            (kosongkan jika shift masih berjalan)
                        </span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="closing_cash"
                               class="form-control @error('closing_cash') is-invalid @enderror"
                               value="{{ old('closing_cash', $shift->closing_cash) }}"
                               min="0" step="1000">
                    </div>
                    @error('closing_cash')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="3"
                              placeholder="Catatan shift (opsional)">{{ old('notes', $shift->notes) }}</textarea>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark px-4">Update Shift</button>
                <a href="{{ route('admin.shifts.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>

        </form>
    </div>
</div>

@endsection