@extends('layouts.app')
@section('page-title', 'Dashboard Kasir')
@section('content')

<link rel="stylesheet" href="{{ asset('css/orders.css') }}">

<div class="page-header">
    <h5>Dashboard Kasir</h5>
    <p>Selamat datang, {{ auth()->user()->name }}! — {{ now()->format('l, d F Y') }}</p>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="summary-card" style="background: white;">
            <div>
                <div class="summary-card-label">Total Order Hari Ini</div>
                <div class="summary-card-value">{{ $summary['total_orders'] }}</div>
            </div>
            <div class="summary-card-icon">📋</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="summary-card" style="background: white;">
            <div>
                <div class="summary-card-label">Selesai</div>
                <div class="summary-card-value">{{ $summary['total_completed'] }}</div>
            </div>
            <div class="summary-card-icon">✅</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="summary-card" style="background: white;">
            <div>
                <div class="summary-card-label">Dibatalkan</div>
                <div class="summary-card-value">{{ $summary['total_cancelled'] }}</div>
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

{{-- Quick Actions --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <a href="{{ route('kasir.pos.index') }}" 
           style="text-decoration: none;">
            <div class="summary-card" style="background: #2c3e50; cursor: pointer;">
                <div>
                    <div class="summary-card-label" style="color: rgba(255,255,255,0.7);">Buka POS</div>
                    <div class="summary-card-value" style="color: white;">Buat Order Baru</div>
                </div>
                <div class="summary-card-icon">🧾</div>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('kasir.orders.index') }}"
           style="text-decoration: none;">
            <div class="summary-card" style="background: white; cursor: pointer;">
                <div>
                    <div class="summary-card-label">Riwayat Order</div>
                    <div class="summary-card-value">Lihat Transaksi</div>
                </div>
                <div class="summary-card-icon">📊</div>
            </div>
        </a>
    </div>
</div>

{{-- Recent Orders --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span style="font-size: 14px; font-weight: 600;">Order Terbaru Hari Ini</span>
        <a href="{{ route('kasir.orders.index') }}" style="font-size: 13px; color: #2c3e50;">
            Lihat semua →
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Kode Order</th>
                    <th>Tipe</th>
                    <th>Meja</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_orders as $order)
                <tr>
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
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4" style="color: #888;">
                        Belum ada order hari ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection