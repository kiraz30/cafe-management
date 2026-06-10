@extends('layouts.app')
@section('page-title', 'Detail Pembelian')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h5>Detail Pembelian</h5>
        <p>{{ $purchase->purchase_code }}</p>
    </div>
    <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <span style="font-size: 14px; font-weight: 600;">Info Pembelian</span>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <div style="font-size: 12px; color: #888;">Kode PO</div>
                    <div style="font-weight: 700; font-family: monospace;">{{ $purchase->purchase_code }}</div>
                </div>
                <div class="mb-3">
                    <div style="font-size: 12px; color: #888;">Supplier</div>
                    <div style="font-weight: 600;">{{ $purchase->supplier->name }}</div>
                </div>
                <div class="mb-3">
                    <div style="font-size: 12px; color: #888;">Tanggal</div>
                    <div>{{ $purchase->purchase_date->format('d M Y') }}</div>
                </div>
                <div class="mb-3">
                    <div style="font-size: 12px; color: #888;">Status</div>
                    @if($purchase->status === 'received')
                        <span class="badge-active">Diterima</span>
                    @elseif($purchase->status === 'pending')
                        <span style="background:#fef9e7;color:#b7770d;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;">Pending</span>
                    @else
                        <span class="badge-inactive">Dibatalkan</span>
                    @endif
                </div>
                <div class="mb-3">
                    <div style="font-size: 12px; color: #888;">Dibuat Oleh</div>
                    <div>{{ $purchase->user->name }}</div>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span style="font-weight: 600;">Total</span>
                    <span style="font-weight: 700; font-size: 16px;">
                        Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <span style="font-size: 14px; font-weight: 600;">Item Pembelian</span>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bahan Baku</th>
                            <th>Satuan</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="font-weight: 600;">{{ $item->ingredient->name }}</td>
                            <td>{{ $item->ingredient->unit }}</td>
                            <td>{{ number_format($item->quantity, 2) }}</td>
                            <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end" style="font-weight: 600;">Total</td>
                            <td style="font-weight: 700;">
                                Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection