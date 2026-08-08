@extends('layouts.app')
@section('page-title', 'Dashboard Admin')
@section('content')

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<div class="page-header">
    <h5>Dashboard Admin</h5>
    <p>{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
</div>

{{-- ===================== --}}
{{-- Statistik Hari Ini   --}}
{{-- ===================== --}}
<div style="font-size: 12px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
    📊 Statistik Hari Ini
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card blue">
            <div>
                <div class="stat-label">Total Order</div>
                <div class="stat-value">{{ $today['total_orders'] }}</div>
                <div class="stat-sub">Semua status</div>
            </div>
            <div class="stat-icon">📋</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div>
                <div class="stat-label">Order Selesai</div>
                <div class="stat-value">{{ $today['total_completed'] }}</div>
                <div class="stat-sub">{{ $today['total_pending'] }} pending</div>
            </div>
            <div class="stat-icon">✅</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card red">
            <div>
                <div class="stat-label">Dibatalkan</div>
                <div class="stat-value">{{ $today['total_cancelled'] }}</div>
                <div class="stat-sub">Hari ini</div>
            </div>
            <div class="stat-icon">❌</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card dark">
            <div>
                <div class="stat-label">Pendapatan Hari Ini</div>
                <div class="stat-value small">
                    Rp {{ number_format($today['total_revenue'], 0, ',', '.') }}
                </div>
                <div class="stat-sub">Dari order selesai</div>
            </div>
            <div class="stat-icon">💰</div>
        </div>
    </div>
</div>

{{-- Pembayaran Split --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card green">
            <div>
                <div class="stat-label">Pendapatan Tunai</div>
                <div class="stat-value small">
                    Rp {{ number_format($today['total_cash'], 0, ',', '.') }}
                </div>
            </div>
            <div class="stat-icon">💵</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card blue">
            <div>
                <div class="stat-label">Pendapatan QRIS</div>
                <div class="stat-value small">
                    Rp {{ number_format($today['total_qris'], 0, ',', '.') }}
                </div>
            </div>
            <div class="stat-icon">📱</div>
        </div>
    </div>
</div>

{{-- ===================== --}}
{{-- Statistik Bulan Ini  --}}
{{-- ===================== --}}
<div style="font-size: 12px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
    📅 Statistik Bulan Ini — {{ now()->isoFormat('MMMM Y') }}
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card blue">
            <div>
                <div class="stat-label">Total Order</div>
                <div class="stat-value">{{ $thisMonth['total_orders'] }}</div>
                <div class="stat-sub">Bulan ini</div>
            </div>
            <div class="stat-icon">📋</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card dark">
            <div>
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value small">
                    Rp {{ number_format($thisMonth['total_revenue'], 0, ',', '.') }}
                </div>
                <div class="stat-sub">Bulan ini</div>
            </div>
            <div class="stat-icon">💰</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card green">
            <div>
                <div class="stat-label">Rata-rata / Hari</div>
                <div class="stat-value small">
                    Rp {{ number_format($thisMonth['avg_per_day'], 0, ',', '.') }}
                </div>
                <div class="stat-sub">{{ now()->day }} hari berjalan</div>
            </div>
            <div class="stat-icon">📈</div>
        </div>
    </div>
</div>

{{-- ===================== --}}
{{-- Grafik 7 Hari        --}}
{{-- ===================== --}}
<div class="section-card mb-4">
    <div class="section-header">
        <div class="section-title">📈 Grafik Penjualan 7 Hari Terakhir</div>
        <div class="section-subtitle">Pendapatan & jumlah order</div>
    </div>
    <div class="section-body">
        <div class="chart-wrap">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
</div>

{{-- ===================== --}}
{{-- Menu Terlaris & Stok --}}
{{-- ===================== --}}
<div class="row g-4 mb-4">

    {{-- Menu Terlaris --}}
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">🏆 Menu Terlaris</div>
                <div class="section-subtitle">Top 5 all time</div>
            </div>
            <div class="section-body">
                @forelse($topMenus as $index => $item)
                <div class="top-menu-item">
                    <div class="top-menu-rank {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="top-menu-info">
                        <div class="top-menu-name">{{ $item->menu->name }}</div>
                        <div class="top-menu-stats">
                            Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                        </div>
                    </div>
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

    {{-- Stok Menipis --}}
    <div class="col-md-6">
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">⚠️ Stok Menipis</div>
                <div class="section-subtitle">Bahan baku di bawah minimum</div>
            </div>
            <div class="section-body">
                @forelse($lowStocks as $ingredient)
                <div class="low-stock-item">
                    <div>
                        <div class="low-stock-name">{{ $ingredient->name }}</div>
                        <div class="low-stock-unit">Min: {{ $ingredient->min_stock }} {{ $ingredient->unit }}</div>
                    </div>
                    <div class="low-stock-qty">
                        <div class="low-stock-current">
                            {{ number_format($ingredient->stock_quantity, 2) }} {{ $ingredient->unit }}
                        </div>
                        <div class="low-stock-min">Stok saat ini</div>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <div class="empty-state-icon">✅</div>
                    <div class="empty-state-text">Semua stok aman</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ===================== --}}
{{-- Shift & User Stats   --}}
{{-- ===================== --}}
<div class="row g-4">

    {{-- Shift Hari Ini --}}
    <div class="col-md-8">
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">🕐 Shift Hari Ini</div>
                <div class="section-subtitle">{{ $activeShifts->count() }} karyawan</div>
            </div>
            <div class="section-body">
                @forelse($activeShifts as $shift)
                <div class="shift-item">
                    <div class="shift-avatar">
                        {{ strtoupper(substr($shift->user->name, 0, 1)) }}
                    </div>
                    <div class="shift-info">
                        <div class="shift-name">{{ $shift->user->name }}</div>
                        <div class="shift-time">
                            {{ $shift->start_time->format('H:i') }} —
                            {{ $shift->end_time ? $shift->end_time->format('H:i') : 'Sedang berjalan' }}
                        </div>
                    </div>
                    <span class="shift-role {{ $shift->user->role }}">
                        {{ ucfirst($shift->user->role) }}
                    </span>
                </div>
                @empty
                <div class="empty-state">
                    <div class="empty-state-icon">🕐</div>
                    <div class="empty-state-text">Belum ada shift hari ini</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- User Stats --}}
    <div class="col-md-4">
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">👥 User Aktif</div>
                <div class="section-subtitle">Per role</div>
            </div>
            <div class="section-body">
                @foreach(['admin', 'kasir', 'barista', 'pelayan'] as $role)
                <div class="user-stat-item">
                    <span>{{ ucfirst($role) }}</span>
                    <span style="font-weight: 700;">{{ $userStats[$role] ?? 0 }}</span>
                </div>
                @endforeach
                <div class="user-stat-item" style="border-top: 2px solid #f0f0f0; margin-top: 4px; padding-top: 12px;">
                    <span style="font-weight: 700;">Total</span>
                    <span style="font-weight: 700;">{{ $userStats->sum() }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div id="chartData"
     data-labels='@json($chartLabels)'
     data-revenue='@json($chartRevenue)'
     data-orders='@json($chartOrders)'>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData    = document.getElementById('chartData');
    const labels       = JSON.parse(chartData.dataset.labels);
    const revenueData  = JSON.parse(chartData.dataset.revenue);
    const ordersData   = JSON.parse(chartData.dataset.orders);

    const ctx = document.getElementById('salesChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pendapatan (Rp)',
                    data: revenueData,
                    backgroundColor: 'rgba(44, 62, 80, 0.8)',
                    borderColor: 'rgba(44, 62, 80, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    yAxisID: 'y',
                },
                {
                    label: 'Jumlah Order',
                    data: ordersData,
                    type: 'line',
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#27ae60',
                    pointRadius: 4,
                    tension: 0.4,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return ' Rp ' + context.raw.toLocaleString('id-ID');
                            }
                            return ' ' + context.raw + ' order';
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: {
                        callback: value => 'Rp ' + value.toLocaleString('id-ID'),
                        font: { size: 11 }
                    },
                    grid: { color: '#f0f0f0' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    ticks: {
                        callback: value => value + ' order',
                        font: { size: 11 }
                    },
                    grid: { drawOnChartArea: false }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { color: '#f0f0f0' }
                }
            }
        }
    });
});
</script>

@endsection