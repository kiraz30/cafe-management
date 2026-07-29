@extends('layouts.app')
@section('page-title', 'Kitchen Display')
@section('content')

<link rel="stylesheet" href="{{ asset('css/barista.css') }}">

<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h5>Kitchen Display</h5>
        <p>Antrian pesanan masuk — {{ now()->format('l, d F Y') }}</p>
    </div>
    <div class="refresh-badge">
        <div class="refresh-dot"></div>
        Auto refresh <span id="countdown">30</span>s
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="summary-card" style="border-left: 4px solid #b7770d;">
            <div>
                <div class="summary-card-label">Menunggu Diproses</div>
                <div class="summary-card-value" style="color: #b7770d;">
                    {{ $summary['total_pending'] }}
                </div>
            </div>
            <div class="summary-card-icon">⏳</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card" style="border-left: 4px solid #1a6fa8;">
            <div>
                <div class="summary-card-label">Sedang Diproses</div>
                <div class="summary-card-value" style="color: #1a6fa8;">
                    {{ $summary['total_processing'] }}
                </div>
            </div>
            <div class="summary-card-icon">👨‍🍳</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card" style="border-left: 4px solid #1e8449;">
            <div>
                <div class="summary-card-label">Selesai Hari Ini</div>
                <div class="summary-card-value" style="color: #1e8449;">
                    {{ $summary['total_completed'] }}
                </div>
            </div>
            <div class="summary-card-icon">✅</div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Kolom Pending --}}
    <div class="col-md-6">
        <div class="section-title">
            ⏳ Menunggu Diproses
            <span>{{ $pending_orders->count() }} pesanan</span>
        </div>

        @forelse($pending_orders as $order)
        <div class="order-card pending">
            <div class="order-card-header">
                <div>
                    <div class="order-card-code">{{ $order->order_code }}</div>
                    <div class="order-card-time">
                        {{ $order->created_at->format('H:i') }} —
                        {{ $order->created_at->diffForHumans() }}
                    </div>
                </div>
                <div class="order-card-meta">
                    <span class="badge-status badge-pending">Pending</span>
                </div>
            </div>

            <div class="order-card-body">
                <div class="order-card-items">
                    @foreach($order->items as $item)
                    <div class="order-item-row">
                        <div>
                            <div class="order-item-name">{{ $item->menu->name }}</div>
                            @if($item->notes)
                            <div class="order-item-notes">📝 {{ $item->notes }}</div>
                            @endif
                        </div>
                        <span class="order-item-qty">x{{ $item->quantity }}</span>
                    </div>
                    @endforeach
                </div>

                @if($order->notes)
                <div style="font-size: 12px; color: #888; font-style: italic; margin-bottom: 10px;">
                    📝 Catatan: {{ $order->notes }}
                </div>
                @endif

                <form method="POST"
                      action="{{ route('barista.orders.update-status', $order) }}">
                    @csrf
                    <button type="submit" class="btn-process w-100">
                        👨‍🍳 Mulai Proses
                    </button>
                </form>
            </div>

            <div class="order-card-footer">
                <div class="order-card-table">
                    🪑 {{ $order->table ? 'Meja ' . $order->table->table_number : 'Tanpa Meja' }}
                </div>
                <span class="badge-type {{ $order->order_type === 'dine_in' ? 'badge-dine-in' : 'badge-take-away' }}">
                    {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                </span>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-state-icon">✅</div>
            <div class="empty-state-text">Tidak ada pesanan yang menunggu</div>
        </div>
        @endforelse
    </div>

    {{-- Kolom Processing --}}
    <div class="col-md-6">
        <div class="section-title">
            👨‍🍳 Sedang Diproses
            <span>{{ $processing_orders->count() }} pesanan</span>
        </div>

        @forelse($processing_orders as $order)
        <div class="order-card processing">
            <div class="order-card-header">
                <div>
                    <div class="order-card-code">{{ $order->order_code }}</div>
                    <div class="order-card-time">
                        {{ $order->created_at->format('H:i') }} —
                        {{ $order->created_at->diffForHumans() }}
                    </div>
                </div>
                <div class="order-card-meta">
                    <span class="badge-status badge-processing">Diproses</span>
                </div>
            </div>

            <div class="order-card-body">
                <div class="order-card-items">
                    @foreach($order->items as $item)
                    <div class="order-item-row">
                        <div>
                            <div class="order-item-name">{{ $item->menu->name }}</div>
                            @if($item->notes)
                            <div class="order-item-notes">📝 {{ $item->notes }}</div>
                            @endif
                        </div>
                        <span class="order-item-qty">x{{ $item->quantity }}</span>
                    </div>
                    @endforeach
                </div>

                @if($order->notes)
                <div style="font-size: 12px; color: #888; font-style: italic; margin-bottom: 10px;">
                    📝 Catatan: {{ $order->notes }}
                </div>
                @endif

                <form method="POST"
                      action="{{ route('barista.orders.update-status', $order) }}">
                    @csrf
                    <button type="submit" class="btn-complete w-100">
                        ✅ Selesai
                    </button>
                </form>
            </div>

            <div class="order-card-footer">
                <div class="order-card-table">
                    🪑 {{ $order->table ? 'Meja ' . $order->table->table_number : 'Tanpa Meja' }}
                </div>
                <span class="badge-type {{ $order->order_type === 'dine_in' ? 'badge-dine-in' : 'badge-take-away' }}">
                    {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                </span>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-state-icon">👨‍🍳</div>
            <div class="empty-state-text">Tidak ada pesanan yang sedang diproses</div>
        </div>
        @endforelse
    </div>

</div>

{{-- Auto Refresh --}}
<script>
let countdown = 30;
const countdownEl = document.getElementById('countdown');

setInterval(() => {
    countdown--;
    countdownEl.textContent = countdown;
    if (countdown <= 0) {
        window.location.reload();
    }
}, 1000);
</script>

@endsection