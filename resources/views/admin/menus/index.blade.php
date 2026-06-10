@extends('layouts.app')
@section('page-title', 'Manajemen Menu')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h5>Manajemen Menu</h5>
        <p>Kelola daftar menu cafe</p>
    </div>
    <a href="{{ route('admin.menus.create') }}" class="btn btn-dark btn-sm px-3">
        + Tambah Menu
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span style="font-size: 14px; font-weight: 600;">Daftar Menu</span>
        <span style="font-size: 12px; color: #888;">Total: {{ $menus->total() }} menu</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Gambar</th>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Ketersediaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $menu)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <img src="{{ $menu->imageUrl() }}"
                                 alt="{{ $menu->name }}"
                                 style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px;">
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $menu->name }}</div>
                            <div style="font-size: 12px; color: #888;">{{ Str::limit($menu->description, 40) }}</div>
                        </td>
                        <td>
                            <span class="badge-role badge-kasir">{{ $menu->category->name }}</span>
                        </td>
                        <td style="font-weight: 600;">
                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($menu->is_available)
                                <span class="badge-active">Tersedia</span>
                            @else
                                <span class="badge-inactive">Tidak Tersedia</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.menus.edit', $menu) }}"
                               class="btn btn-outline-secondary btn-action me-1">Edit</a>
                            <form action="{{ route('admin.menus.destroy', $menu) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus menu ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4" style="color: #888;">
                            Belum ada menu.
                            <a href="{{ route('admin.menus.create') }}">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($menus->hasPages())
    <div class="card-footer bg-white py-3">{{ $menus->links() }}</div>
    @endif
</div>

@endsection