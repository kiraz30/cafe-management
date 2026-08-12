@extends('layouts.app')
@section('page-title', 'Laporan Penjualan')
@section('content')

<link rel="stylesheet" href="{{ asset('css/report.css') }}">

<div class="page-header">
    <h5>Laporan Penjualan</h5>
    <p>{{ $periodLabel }}</p>
</div>

{{-- Filter Bar --}}
<div class="filter-bar mb-4">

    {{-- Filter Cepat --}}
    <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;">
        @foreach([
            'today'      => '📅 Hari Ini',
            'last7days'  => '📅 7 Hari Terakhir',
            'this_month' => '📅 Bulan Ini',
            'last_month' => '📅 Bulan Lalu',
            'custom'     => '✏️ Custom',
        ] as $key => $label)
        <a href="{{ $key !== 'custom' ? route('admin.reports.index', ['period' => $key]) : '#' }}"
           class="filter-btn {{ $period === $key ? 'filter-btn-apply' : 'filter-btn-reset' }}"
           onclick="{{ $key === 'custom' ? 'toggleCustom(); return false;' : '' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Custom Date Range --}}
    <div id="customDateForm" @if($period !== 'custom') style="display:none" @endif>
        <form method="GET" action="{{ route('admin.reports.index') }}">
            <input type="hidden" name="period" value="custom">
            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
                <div class="filter-group">
                    <span class="filter-label">Dari Tanggal</span>
                    <input type="date" name="start_date" class="filter-input"
                           value="{{ $period === 'custom' ? $startDate : '' }}">
                </div>
                <div class="filter-group">
                    <span class="filter-label">Sampai Tanggal</span>
                    <input type="date" name="end_date" class="filter-input"
                           value="{{ $period === 'custom' ? $endDate : '' }}">
                </div>
                <div class="filter-group">
                    <span class="filter-label">&nbsp;</span>
                    <button type="submit" class="filter-btn filter-btn-apply">Tampilkan</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Export --}}
    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f0f0f0;">
        <a href="{{ route('admin.reports.export.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="filter-btn filter-btn-export">
            📥 Export Excel
        </a>
        <span style="font-size: 12px; color: #888; margin-left: 8px;">
            Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM Y') }}
            — {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM Y') }}
        </span>
    </div>

</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card blue">
            <div>
                <div class="stat-label">Total Order</div>
                <div class="stat-value">{{ $summary['total_orders'] }}</div>
                <div class="stat-sub">Semua status</div>
            </div>
            <div class="stat-icon">📋</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div>
                <div class="stat-label">Order Selesai</div>
                <div class="stat-value">{{ $summary['total_completed'] }}</div>
                <div class="stat-sub">{{ $summary['total_pending'] }} pending</div>
            </div>
            <div class="stat-icon">✅</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card red">
            <div>
                <div class="stat-label">Dibatalkan</div>
                <div class="stat-value">{{ $summary['total_cancelled'] }}</div>
            </div>
            <div class="stat-icon">❌</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card dark">
            <div>
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value small">
                    Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
                </div>
            </div>
            <div class="stat-icon">💰</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card green">
            <div>
                <div class="stat-label">Tunai (Cash)</div>
                <div class="stat-value small">
                    Rp {{ number_format($summary['total_cash'], 0, ',', '.') }}
                </div>
            </div>
            <div class="stat-icon">💵</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card blue">
            <div>
                <div class="stat-label">QRIS</div>
                <div class="stat-value small">
                    Rp {{ number_format($summary['total_qris'], 0, ',', '.') }}
                </div>
            </div>
            <div class="stat-icon">📱</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card yellow">
            <div>
                <div class="stat-label">Dine In</div>
                <div class="stat-value">{{ $summary['total_dine_in'] }}</div>
            </div>
            <div class="stat-icon">🪑</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card yellow">
            <div>
                <div class="stat-label">Take Away</div>
                <div class="stat-value">{{ $summary['total_take_away'] }}</div>
            </div>
            <div class="stat-icon">🥡</div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Top Menu --}}
    <div class="col-md-4">
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">🏆 Menu Terlaris</div>
                <div class="section-subtitle">Periode ini</div>
            </div>
            <div class="section-body">
                @forelse($topMenus as $index => $item)
                <div class="top-menu-item">
                    <div class="top-menu-rank {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="top-menu-name">{{ $item->menu->name }}</div>
                    <div class="top-menu-qty">{{ $item->total_qty }}x</div>
                </div>
                @empty
                <div class="empty-state">
                    <div class="empty-state-icon">🍽️</div>
                    <div class="empty-state-text">Belum ada data penjualan</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Detail Transaksi --}}
    <div class="col-md-8">
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">📋 Detail Transaksi</div>
                <div class="section-subtitle">{{ $orders->count() }} transaksi</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table report-table mb-0">
                        <thead>
                            <tr>
                                <th>Kode Order</th>
                                <th>Tanggal</th>
                                <th>Kasir</th>
                                <th>Tipe</th>
                                <th>Total</th>
                                <th>Bayar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       style="font-family: monospace; font-weight: 600; color: #2c3e50; text-decoration: none;">
                                        {{ $order->order_code }}
                                    </a>
                                </td>
                                <td style="font-size: 12px; color: #888;">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td style="font-size: 12px;">{{ $order->user->name }}</td>
                                <td>
                                    <span style="font-size: 11px;">
                                        {{ $order->order_type === 'dine_in' ? '🪑 Dine In' : '🥡 Take Away' }}
                                    </span>
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
                                            'completed'  => 'Selesai',
                                            'cancelled'  => 'Dibatalkan',
                                            'pending'    => 'Pending',
                                            'processing' => 'Diproses',
                                            default      => $order->status
                                        } }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4" style="color: #888;">
                                    Belum ada transaksi pada periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($orders->count() > 0)
                        <tfoot>
                            <tr style="background: #f8f9fa;">
                                <td colspan="4" style="font-weight: 700; padding: 10px 14px;">
                                    Total Pendapatan
                                </td>
                                <td style="font-weight: 700; color: #1e8449; padding: 10px 14px;">
                                    Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function toggleCustom() {
    const form = document.getElementById('customDateForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>

@endsection