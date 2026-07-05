@extends('layouts.app')
@section('page-title', 'Riwayat Order Hari Ini')
@section('content')

<link rel="stylesheet" href="{{ asset('css/orders.css') }}">

<div class="page-header">
    <h5>Riwayat Order Hari Ini</h5>
    <p>{{ now()->format('l, d F Y') }}</p>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="summary-card" style="background: white;">
            <div>
                <div class="summary-card-label">Total Order</div>
                <div class="summary-card-value">{{ number_format($summary['total_orders']) }}</div>
            </div>
            <div class="summary-card-icon">📋</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="summary-card" style="background: white;">
            <div>
                <div class="summary-card-label">Selesai</div>
                <div class="summary-card-value">{{ number_format($summary['total_completed']) }}</div>
            </div>
            <div class="summary-card-icon">✅</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="summary-card" style="background: white;">
            <div>
                <div class="summary-card-label">Dibatalkan</div>
                <div class="summary-card-value">{{ number_format($summary['total_cancelled']) }}</div>
            </div>
            <div class="summary-card-icon">❌</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="summary-card" style="background: white;">
            <div>
                <div class="summary-card-label">Pendapatan Hari Ini</div>
                <div class="summary-card-value" style="font-size: 16px;">
                    Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
                </div>
            </div>
            <div class="summary-card-icon">💰</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('kasir.orders.index') }}">
        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="status" class="filter-select">
                <option value="">Semua</option>
                <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Tipe</span>
            <select name="order_type" class="filter-select">
                <option value="">Semua</option>
                <option value="dine_in"   {{ request('order_type') == 'dine_in'   ? 'selected' : '' }}>Dine In</option>
                <option value="take_away" {{ request('order_type') == 'take_away' ? 'selected' : '' }}>Take Away</option>
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">&nbsp;</span>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="filter-btn filter-btn-apply">Filter</button>
                <a href="{{ route('kasir.orders.index') }}" class="filter-btn filter-btn-reset">Reset</a>
            </div>
        </div>
    </form>
</div>

{{-- Tabel Order --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span style="font-size: 14px; font-weight: 600;">Daftar Order</span>
        <a href="{{ route('kasir.pos.index') }}" class="btn btn-dark btn-sm px-3">
            + Order Baru
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Order</th>
                        <th>Tipe</th>
                        <th>Meja</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span style="font-family: monospace; font-weight: 600;">
                                {{ $order->order_code }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-type {{ $order->order_type === 'dine_in' ? 'badge-dine-in' : 'badge-take-away' }}">
                                {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                            </span>
                        </td>
                        <td style="font-size: 13px;">
                            {{ $order->table ? 'Meja ' . $order->table->table_number : '-' }}
                        </td>
                        <td style="font-weight: 600;">
                            Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($order->payment)
                            <span class="badge-payment badge-{{ $order->payment->payment_method }}">
                                {{ $order->payment->payment_method === 'cash' ? 'Tunai' : 'QRIS' }}
                            </span>
                            @else
                            <span style="color: #bbb; font-size: 12px;">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status badge-{{ $order->status }}">
                                {{ match($order->status) {
                                    'pending'    => 'Pending',
                                    'processing' => 'Diproses',
                                    'completed'  => 'Selesai',
                                    'cancelled'  => 'Dibatalkan',
                                    default      => $order->status
                                } }}
                            </span>
                        </td>
                        <td style="font-size: 12px; color: #888;">
                            {{ $order->created_at->format('H:i') }}
                        </td>
                        <td>
                            <a href="{{ route('kasir.orders.show', $order) }}"
                               class="btn btn-outline-info btn-action me-1">Detail</a>
                            <a href="{{ route('kasir.pos.receipt', $order) }}"
                               target="_blank"
                               class="btn btn-outline-secondary btn-action">Struk</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4" style="color: #888;">
                            Belum ada order hari ini.
                            <a href="{{ route('kasir.pos.index') }}">Buat order baru</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white py-3">{{ $orders->links() }}</div>
    @endif
</div>

@endsection