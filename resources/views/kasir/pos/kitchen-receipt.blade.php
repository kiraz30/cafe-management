<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Dapur #{{ $order->order_code }}</title>
    <link rel="stylesheet" href="{{ asset('css/kitchen-receipt.css') }}">
</head>
<body>

<div>
    <div class="kitchen-receipt">

        {{-- Header --}}
        <div class="kitchen-header">
            <div class="kitchen-title">🍳 DAPUR</div>
            <div class="kitchen-subtitle">{{ $settings['cafe_name'] }}</div>
        </div>

        <hr class="divider">

        {{-- Info Order --}}
        <div class="info-row">
            <span class="info-label">No. Order</span>
            <span class="info-value">{{ $order->order_code }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Waktu</span>
            <span class="info-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kasir</span>
            <span class="info-value">{{ $order->user->name }}</span>
        </div>

        {{-- Badge Meja --}}
        <div class="meja-badge">
            <div class="meja-label">NOMOR MEJA</div>
            @if($order->table)
                <div class="meja-number">{{ $order->table->table_number }}</div>
            @else
                <div class="meja-number">-</div>
            @endif
            <div class="meja-type">
                {{ $order->order_type === 'dine_in' ? '🪑 Dine In' : '🥡 Take Away' }}
            </div>
        </div>

        <hr class="divider">

        {{-- Items --}}
        <div class="kitchen-items">
            @foreach($order->items as $item)
            <div class="kitchen-item">
                <div class="kitchen-item-top">
                    <div class="kitchen-item-name">{{ $item->menu->name }}</div>
                    <div class="kitchen-item-qty">x{{ $item->quantity }}</div>
                </div>
                @if($item->notes)
                <div class="kitchen-item-notes">📝 {{ $item->notes }}</div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Catatan Order --}}
        @if($order->notes)
        <div class="order-notes">
            <div class="order-notes-label">⚠️ CATATAN ORDER</div>
            <div class="order-notes-text">{{ $order->notes }}</div>
        </div>
        @endif

        <hr class="divider">

        {{-- Footer --}}
        <div class="kitchen-footer">
            Dicetak: {{ now()->format('d/m/Y H:i:s') }}
        </div>

    </div>

    {{-- Tombol Print --}}
    <button class="print-btn" onclick="window.print()">
        🖨️ Cetak Struk Dapur
    </button>
</div>

</body>
</html>