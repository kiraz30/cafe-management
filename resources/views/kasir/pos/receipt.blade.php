<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->order_code }}</title>
    <link rel="stylesheet" href="{{ asset('css/receipt.css') }}">
</head>
<body>

<div>
    <div class="receipt">

        {{-- Header --}}
        <div class="receipt-header">
            @if($settings['receipt_show_logo'] == '1' && !empty($settings['cafe_logo']))
            <img src="{{ asset('storage/' . $settings['cafe_logo']) }}"
                 class="receipt-logo" alt="Logo"><br>
            @endif
            <div class="receipt-cafe-name">{{ $settings['cafe_name'] }}</div>
            @if(!empty($settings['cafe_address']))
            <div class="receipt-cafe-address">{{ $settings['cafe_address'] }}</div>
            @endif
            @if(!empty($settings['cafe_phone']))
            <div class="receipt-cafe-phone">Telp: {{ $settings['cafe_phone'] }}</div>
            @endif
            @if(!empty($settings['receipt_header']))
            <div class="receipt-header-text">{{ $settings['receipt_header'] }}</div>
            @endif
        </div>

        <hr class="divider">

        {{-- Info Order --}}
        <div class="receipt-info">
            <div class="receipt-info-row">
                <span>No. Order</span>
                <span>{{ $order->order_code }}</span>
            </div>
            <div class="receipt-info-row">
                <span>Tanggal</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="receipt-info-row">
                <span>Kasir</span>
                <span>{{ $order->user->name }}</span>
            </div>
            <div class="receipt-info-row">
                <span>Tipe</span>
                <span>{{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}</span>
            </div>
            @if($order->table)
            <div class="receipt-info-row">
                <span>Meja</span>
                <span>{{ $order->table->table_number }}</span>
            </div>
            @endif
        </div>

        <hr class="divider">

        {{-- Items --}}
        <div class="receipt-items">
            @foreach($order->items as $item)
            <div class="receipt-item">
                <div class="receipt-item-name">{{ $item->menu->name }}</div>
                <div class="receipt-item-detail">
                    <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($item->notes)
                <div class="receipt-item-notes">* {{ $item->notes }}</div>
                @endif
            </div>
            @endforeach
        </div>

        <hr class="divider">

        {{-- Summary --}}
        <div class="receipt-summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            @if($order->tax_amount > 0)
            <div class="summary-row">
                <span>Pajak</span>
                <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($order->discount_amount > 0)
            <div class="summary-row">
                <span>Diskon</span>
                <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="summary-row total">
                <span>TOTAL</span>
                <span>Rp {{ number_format($order->final_amount, 0, ',', '.') }}</span>
            </div>
            @if($order->payment)
            <div class="summary-row payment">
                <span>{{ $order->payment->payment_method === 'cash' ? 'Tunai' : 'QRIS' }}</span>
                <span>Rp {{ number_format($order->payment->amount_paid, 0, ',', '.') }}</span>
            </div>
            @if($order->payment->payment_method === 'cash')
            <div class="summary-row change">
                <span>Kembali</span>
                <span>Rp {{ number_format($order->payment->amount_paid - $order->final_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            @endif
        </div>

        <hr class="divider">

        {{-- Footer --}}
        <div class="receipt-footer">
            @if(!empty($settings['receipt_footer']))
            <div class="receipt-footer-text">{{ $settings['receipt_footer'] }}</div>
            @endif
            <div class="receipt-thank">Terima Kasih! ☕</div>
        </div>

    </div>

    <button class="print-btn" onclick="window.print()">
        🖨️ Cetak Struk
    </button>
</div>

</body>
</html>