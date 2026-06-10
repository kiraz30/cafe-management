@extends('layouts.app')
@section('page-title', 'Pembelian')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h5>Manajemen Pembelian</h5>
        <p>Kelola pembelian bahan baku dari supplier</p>
    </div>
    <a href="{{ route('admin.purchases.create') }}" class="btn btn-dark btn-sm px-3">
        + Buat Pembelian
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span style="font-size: 14px; font-weight: 600;">Daftar Pembelian</span>
        <span style="font-size: 12px; color: #888;">Total: {{ $purchases->total() }} transaksi</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode PO</th>
                        <th>Supplier</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><div style="font-weight: 600; font-family: monospace;">{{ $purchase->purchase_code }}</div></td>
                        <td>{{ $purchase->supplier->name }}</td>
                        <td style="font-size: 13px;">{{ $purchase->purchase_date->format('d M Y') }}</td>
                        <td>Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                        <td>
                            @if($purchase->status === 'received')
                                <span class="badge-active">Diterima</span>
                            @elseif($purchase->status === 'pending')
                                <span style="background:#fef9e7;color:#b7770d;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;">Pending</span>
                            @else
                                <span class="badge-inactive">Dibatalkan</span>
                            @endif
                        </td>
                        <td style="font-size: 13px;">{{ $purchase->user->name }}</td>
                        <td>
                            <a href="{{ route('admin.purchases.show', $purchase) }}"
                               class="btn btn-outline-info btn-action me-1">Detail</a>
                            <form action="{{ route('admin.purchases.destroy', $purchase) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus data pembelian ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4" style="color: #888;">
                            Belum ada data pembelian.
                            <a href="{{ route('admin.purchases.create') }}">Buat sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($purchases->hasPages())
    <div class="card-footer bg-white py-3">{{ $purchases->links() }}</div>
    @endif
</div>

@endsection