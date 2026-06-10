@extends('layouts.app')
@section('page-title', 'Manajemen Supplier')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h5>Manajemen Supplier</h5>
        <p>Kelola data supplier bahan baku</p>
    </div>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-dark btn-sm px-3">
        + Tambah Supplier
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span style="font-size: 14px; font-weight: 600;">Daftar Supplier</span>
        <span style="font-size: 12px; color: #888;">Total: {{ $suppliers->total() }} supplier</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Supplier</th>
                        <th>Contact Person</th>
                        <th>No. Telepon</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><div style="font-weight: 600;">{{ $supplier->name }}</div></td>
                        <td>{{ $supplier->contact_person ?? '-' }}</td>
                        <td>{{ $supplier->phone ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td>
                            @if($supplier->is_active)
                                <span class="badge-active">Aktif</span>
                            @else
                                <span class="badge-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                               class="btn btn-outline-secondary btn-action me-1">Edit</a>
                            <form action="{{ route('admin.suppliers.destroy', $supplier) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus supplier ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4" style="color: #888;">
                            Belum ada supplier.
                            <a href="{{ route('admin.suppliers.create') }}">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer bg-white py-3">{{ $suppliers->links() }}</div>
    @endif
</div>

@endsection