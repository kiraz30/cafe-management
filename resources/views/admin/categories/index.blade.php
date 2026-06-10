@extends('layouts.app')
@section('page-title', 'Manajemen Kategori')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h5>Manajemen Kategori</h5>
        <p>Kelola kategori menu cafe</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-dark btn-sm px-3">
        + Tambah Kategori
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span style="font-size: 14px; font-weight: 600;">Daftar Kategori</span>
        <span style="font-size: 12px; color: #888;">Total: {{ $categories->total() }} kategori</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kategori</th>
                        <th>Slug</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Menu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><div style="font-weight: 600;">{{ $category->name }}</div></td>
                        <td>
                            <span style="font-family: monospace; font-size: 12px; background: #f0f0f0; padding: 2px 8px; border-radius: 4px;">
                                {{ $category->slug }}
                            </span>
                        </td>
                        <td style="color: #888; font-size: 13px;">
                            {{ $category->description ?? '-' }}
                        </td>
                        <td>
                            <span class="badge-role badge-kasir">{{ $category->menus_count }} menu</span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge-active">Aktif</span>
                            @else
                                <span class="badge-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="btn btn-outline-secondary btn-action me-1">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4" style="color: #888;">
                            Belum ada kategori.
                            <a href="{{ route('admin.categories.create') }}">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white py-3">{{ $categories->links() }}</div>
    @endif
</div>

@endsection