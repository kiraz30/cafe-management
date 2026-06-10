@extends('layouts.app')

@section('page-title', 'Kelola User')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h5>Kelola User</h5>
        <p>Manajemen akun pengguna sistem</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-dark btn-sm px-3">
        + Tambah User
    </a>
</div>

{{-- Tabel User --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span style="font-size: 14px; font-weight: 600;">Daftar User</span>
        <span style="font-size: 12px; color: #888;">Total: {{ $users->total() }} user</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $user->name }}</div>
                        </td>
                        <td style="color: #888;">{{ $user->email }}</td>
                        <td>
                            <span class="badge-role badge-{{ $user->role }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge-active">Aktif</span>
                            @else
                                <span class="badge-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td style="color: #888; font-size: 13px;">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               class="btn btn-outline-secondary btn-action me-1">
                                Edit
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action">
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4" style="color: #888;">
                            Belum ada user. 
                            <a href="{{ route('admin.users.create') }}">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection