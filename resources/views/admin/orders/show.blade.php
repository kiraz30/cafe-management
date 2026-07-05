@extends('layouts.app')
@section('page-title', 'Detail Order')
@section('content')

<link rel="stylesheet" href="{{ asset('css/orders.css') }}">

<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h5>Detail Order</h5>
        <p>{{ $order->order_code }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('kasir.pos.receipt', $order) }}"
           target="_blank" class="btn btn-outline-dark btn-sm">
            🖨️ Cetak Struk
        </a>
        <a href="{{ route('admin.orders.index') }}"
           class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>
</div>

<div class="row g-4">

    {{-- Info Order --}}
    <div class="col-md-4">
        <div class="order-detail-card card">
            <div class="order-detail-header">Info Order</div>
            <div class="order-detail-body">
                <div class="detail-row">
                    <span class="detail-label">Kode Order</span>
                    <span class="detail-value" style="font-family: monospace;">{{ $order->order_code }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="badge-status badge-{{ $order->status }}">
                        {{ match($order->status) {
                            'pending'    => 'Pending',
                            'processing' => 'Diproses',
                            'completed'  => 'Selesai',
                            'cancelled'  => 'Dibatalkan',
                            default      => $order->status
                        } }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tipe</span>
                    <span class="badge-type {{ $order->order_type === 'dine_in' ? 'badge-dine-in' : 'badge-take-away' }}">
                        {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Meja</span>
                    <span class="detail-value">
                        {{ $order->table ? 'Meja ' . $order->table->table_number : '-' }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Kasir</span>
                    <span class="detail-value">{{ $order->user->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Waktu</span>
                    <span class="detail-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($order->notes)
                <div class="detail-row">
                    <span class="detail-label">Catatan</span>
                    <span class="detail-value">{{ $order->notes }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Info Pembayaran --}}
        @if($order->payment)
        <div class="order-detail-card card">
            <div class="order-detail-header">Info Pembayaran</div>
            <div class="order-detail-body">
                <div class="detail-row">
                    <span class="detail-label">Metode</span>
                    <span class="badge-payment badge-{{ $order->payment->payment_method }}">
                        {{ $order->payment->payment_method === 'cash' ? 'Tunai' : 'QRIS' }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Jumlah Bayar</span>
                    <span class="detail-value">
                        Rp {{ number_format($order->payment->amount_paid, 0, ',', '.') }}
                    </span>
                </div>
                @if($order->payment->payment_method === 'cash')
                <div class="detail-row">
                    <span class="detail-label">Kembalian</span>
                    <span class="detail-value" style="color: #1e8449;">
                        Rp {{ number_format($order->payment->amount_paid - $order->final_amount, 0, ',', '.') }}
                    </span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="badge-status badge-completed">
                        {{ ucfirst($order->payment->status) }}
                    </span>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Item Order --}}
    <div class="col-md-8">
        <div class="order-detail-card card">
            <div class="order-detail-header">Item Pesanan</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Menu</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="font-weight: 600;">{{ $item->menu->name }}</td>
                            <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td style="font-weight: 600;">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                            <td style="font-size: 12px; color: #888;">{{ $item->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white p-3">
                <div class="d-flex flex-column align-items-end gap-1">
                    <div class="detail-row" style="width: 250px;">
                        <span class="detail-label">Subtotal</span>
                        <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($order->tax_amount > 0)
                    <div class="detail-row" style="width: 250px;">
                        <span class="detail-label">Pajak</span>
                        <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($order->discount_amount > 0)
                    <div class="detail-row" style="width: 250px;">
                        <span class="detail-label">Diskon</span>
                        <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="detail-row" style="width: 250px; font-size: 16px; font-weight: 700; color: #2c3e50; border-top: 1.5px solid #e0e0e0; padding-top: 8px; margin-top: 4px;">
                        <span>Total</span>
                        <span>Rp {{ number_format($order->final_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection