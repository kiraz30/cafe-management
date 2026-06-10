@extends('layouts.app')

@section('page-title', 'Manajemen Shift')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h5>Manajemen Shift</h5>
        <p>Kelola jadwal shift karyawan</p>
    </div>
    <a href="{{ route('admin.shifts.create') }}" class="btn btn-dark btn-sm px-3">
        + Tambah Shift
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span style="font-size: 14px; font-weight: 600;">Daftar Shift</span>
        <span style="font-size: 12px; color: #888;">Total: {{ $shifts->total() }} shift</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Karyawan</th>
                        <th>Role</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Durasi</th>
                        <th>Kas Awal</th>
                        <th>Kas Akhir</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $shift)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><div style="font-weight: 600;">{{ $shift->user->name }}</div></td>
                        <td>
                            <span class="badge-role badge-{{ $shift->user->role }}">
                                {{ ucfirst($shift->user->role) }}
                            </span>
                        </td>
                        <td style="font-size: 13px;">
                            {{ $shift->start_time->format('d M Y, H:i') }}
                        </td>
                        <td style="font-size: 13px;">
                            {{ $shift->end_time ? $shift->end_time->format('d M Y, H:i') : '-' }}
                        </td>
                        <td style="font-size: 13px; color: #888;">
                            {{ $shift->duration() }}
                        </td>
                        <td>Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</td>
                        <td>
                            {{ $shift->closing_cash 
                                ? 'Rp ' . number_format($shift->closing_cash, 0, ',', '.') 
                                : '-' }}
                        </td>
                        <td>
                            @if($shift->isActive())
                                <span class="badge-active">Aktif</span>
                            @else
                                <span class="badge-inactive">Selesai</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.shifts.show', $shift) }}" 
                               class="btn btn-outline-info btn-action me-1">
                                Detail
                            </a>
                            <a href="{{ route('admin.shifts.edit', $shift) }}" 
                               class="btn btn-outline-secondary btn-action me-1">
                                Edit
                            </a>
                            <form action="{{ route('admin.shifts.destroy', $shift) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus shift ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4" style="color: #888;">
                            Belum ada data shift.
                            <a href="{{ route('admin.shifts.create') }}">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($shifts->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $shifts->links() }}
    </div>
    @endif
</div>

@endsection