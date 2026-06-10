@extends('layouts.app')
@section('page-title', 'Pengaturan')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
    .nav-tabs .nav-link { color: #666; font-size: 14px; }
    .nav-tabs .nav-link.active { color: #2c3e50; font-weight: 600; }
    .setting-section { margin-bottom: 24px; }
    .setting-section-title {
        font-size: 13px; font-weight: 700; color: #2c3e50;
        text-transform: uppercase; letter-spacing: 0.5px;
        margin-bottom: 16px; padding-bottom: 8px;
        border-bottom: 2px solid #f0f0f0;
    }
    .qris-dynamic { display: none; }
    .qris-static  { display: none; }
</style>

<div class="page-header">
    <h5>Pengaturan Sistem</h5>
    <p>Konfigurasi cafe dan metode pembayaran</p>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
@csrf

<div class="card">
    <div class="card-header">
        {{-- Tab Navigation --}}
        <ul class="nav nav-tabs card-header-tabs" id="settingTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#general">🏪 Info Cafe</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#payment">💳 Pembayaran</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#transaction">🧾 Transaksi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#receipt">🖨️ Struk</a>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content">

            {{-- Tab General --}}
            <div class="tab-pane fade show active" id="general">
                <div class="setting-section">
                    <div class="setting-section-title">Informasi Cafe</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Cafe</label>
                            <input type="text" name="cafe_name" class="form-control"
                                   value="{{ $general['cafe_name']->value ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="cafe_phone" class="form-control"
                                   value="{{ $general['cafe_phone']->value ?? '' }}"
                                   placeholder="08xx-xxxx-xxxx">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="cafe_address" class="form-control" rows="2"
                                      placeholder="Alamat lengkap cafe">{{ $general['cafe_address']->value ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Logo Cafe</label>
                            @if(!empty($general['cafe_logo']->value))
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $general['cafe_logo']->value) }}"
                                     style="height: 60px; object-fit: contain; border-radius: 8px; border: 1px solid #e0e0e0; padding: 4px;">
                            </div>
                            @endif
                            <input type="file" name="cafe_logo" class="form-control" accept="image/*">
                            <div style="font-size: 11px; color: #888; margin-top: 4px;">Format: JPG, PNG. Maks 1MB.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Payment --}}
            <div class="tab-pane fade" id="payment">
                <div class="setting-section">
                    <div class="setting-section-title">Metode Pembayaran</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card border p-3">
                                <div class="form-check">
                                    <input type="checkbox" name="payment_cash" id="payment_cash"
                                           class="form-check-input"
                                           {{ ($payment['payment_cash']->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <label for="payment_cash" class="form-check-label">
                                        <div style="font-weight: 600;">💵 Pembayaran Tunai (Cash)</div>
                                        <div style="font-size: 12px; color: #888;">Customer bayar dengan uang tunai</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border p-3">
                                <div class="form-check">
                                    <input type="checkbox" name="payment_qris" id="payment_qris"
                                           class="form-check-input"
                                           {{ ($payment['payment_qris']->value ?? '1') == '1' ? 'checked' : '' }}
                                           onchange="toggleQris()">
                                    <label for="payment_qris" class="form-check-label">
                                        <div style="font-weight: 600;">📱 Pembayaran QRIS</div>
                                        <div style="font-size: 12px; color: #888;">Customer bayar via scan QR Code</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- QRIS Settings --}}
                    <?php
                        $qrisActive = ($payment['payment_qris']->value ?? '1') == '1';
                        $qrisType   = $payment['qris_type']->value ?? 'static';
                    ?>
                    <div id="qrisSettings" @if(!$qrisActive) style="display:none" @endif>

                        {{-- Pilih Tipe QRIS --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tipe QRIS</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card border p-3" style="cursor:pointer;"
                                         onclick="selectQrisType('static')">
                                        <div class="form-check">
                                            <input type="radio" name="qris_type" value="static"
                                                   id="qris_static" class="form-check-input"
                                                   {{ ($payment['qris_type']->value ?? 'static') == 'static' ? 'checked' : '' }}
                                                   onchange="selectQrisType('static')">
                                            <label for="qris_static" class="form-check-label" style="cursor:pointer;">
                                                <div style="font-weight: 600;">🖼️ QRIS Statis</div>
                                                <div style="font-size: 12px; color: #888; margin-top: 4px;">
                                                    Upload gambar QR Code cafe. Customer scan, kasir konfirmasi manual.
                                                    Cocok untuk cafe kecil-menengah.
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border p-3" style="cursor:pointer;"
                                         onclick="selectQrisType('dynamic')">
                                        <div class="form-check">
                                            <input type="radio" name="qris_type" value="dynamic"
                                                   id="qris_dynamic" class="form-check-input"
                                                   {{ ($payment['qris_type']->value ?? 'static') == 'dynamic' ? 'checked' : '' }}
                                                   onchange="selectQrisType('dynamic')">
                                            <label for="qris_dynamic" class="form-check-label" style="cursor:pointer;">
                                                <div style="font-weight: 600;">⚡ QRIS Dinamis</div>
                                                <div style="font-size: 12px; color: #888; margin-top: 4px;">
                                                    Generate QR per transaksi via API. Nominal otomatis terisi.
                                                    Butuh akun Midtrans/Xendit.
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- QRIS Statis --}}
                        <div id="qrisStaticSettings" class="qris-static">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Upload Gambar QR Code</label>
                                    @if(!empty($payment['qris_image']->value))
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $payment['qris_image']->value) }}"
                                             style="width: 150px; height: 150px; object-fit: contain; border: 1px solid #e0e0e0; border-radius: 8px; padding: 8px;">
                                        <div style="font-size: 11px; color: #888; margin-top: 4px;">QR saat ini</div>
                                    </div>
                                    @endif
                                    <input type="file" name="qris_image" class="form-control" accept="image/*">
                                    <div style="font-size: 11px; color: #888; margin-top: 4px;">
                                        Upload gambar QR Code dari bank/e-wallet cafe. Format: JPG, PNG.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- QRIS Dinamis --}}
                        <div id="qrisDynamicSettings" class="qris-dynamic">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Provider</label>
                                    <select name="qris_provider" class="form-select">
                                        <option value="midtrans" {{ ($payment['qris_provider']->value ?? 'midtrans') == 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                                        <option value="xendit"   {{ ($payment['qris_provider']->value ?? '') == 'xendit' ? 'selected' : '' }}>Xendit</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mode</label>
                                    <select name="qris_mode" class="form-select">
                                        <option value="sandbox"    {{ ($payment['qris_mode']->value ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                        <option value="production" {{ ($payment['qris_mode']->value ?? '') == 'production' ? 'selected' : '' }}>Production (Live)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Server Key</label>
                                    <input type="password" name="qris_server_key" class="form-control"
                                           value="{{ $payment['qris_server_key']->value ?? '' }}"
                                           placeholder="Server key dari dashboard provider">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Client Key</label>
                                    <input type="password" name="qris_client_key" class="form-control"
                                           value="{{ $payment['qris_client_key']->value ?? '' }}"
                                           placeholder="Client key dari dashboard provider">
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-warning" style="font-size: 13px;">
                                        ⚠️ Pastikan sudah memiliki akun merchant aktif di Midtrans atau Xendit sebelum menggunakan mode ini.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Tab Transaction --}}
            <div class="tab-pane fade" id="transaction">
                <div class="setting-section">
                    <div class="setting-section-title">Pengaturan Transaksi</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Pajak (PPN %)</label>
                            <div class="input-group">
                                <input type="number" name="tax_percentage" class="form-control"
                                       value="{{ $transaction['tax_percentage']->value ?? '0' }}"
                                       min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                            <div style="font-size: 11px; color: #888; margin-top: 4px;">Isi 0 jika tidak ada pajak</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Diskon Default (%)</label>
                            <div class="input-group">
                                <input type="number" name="discount_percentage" class="form-control"
                                       value="{{ $transaction['discount_percentage']->value ?? '0' }}"
                                       min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prefix Nomor Order</label>
                            <input type="text" name="order_prefix" class="form-control"
                                   value="{{ $transaction['order_prefix']->value ?? 'ORD' }}"
                                   placeholder="ORD" maxlength="5">
                            <div style="font-size: 11px; color: #888; margin-top: 4px;">
                                Contoh: ORD → ORD-20240101-001
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Receipt --}}
            <div class="tab-pane fade" id="receipt">
                <div class="setting-section">
                    <div class="setting-section-title">Pengaturan Struk</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Header Struk</label>
                            <input type="text" name="receipt_header" class="form-control"
                                   value="{{ $receipt['receipt_header']->value ?? '' }}"
                                   placeholder="Teks di atas struk">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Footer Struk</label>
                            <input type="text" name="receipt_footer" class="form-control"
                                   value="{{ $receipt['receipt_footer']->value ?? '' }}"
                                   placeholder="Teks di bawah struk">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="receipt_show_logo" id="receipt_show_logo"
                                       class="form-check-input"
                                       {{ ($receipt['receipt_show_logo']->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label for="receipt_show_logo" class="form-check-label" style="font-size: 14px;">
                                    Tampilkan logo di struk
                                </label>
                            </div>
                        </div>

                        {{-- Preview Struk --}}
                        <div class="col-md-4">
                            <label class="form-label">Preview Struk</label>
                            <div style="border: 1px dashed #ccc; border-radius: 8px; padding: 16px; font-family: monospace; font-size: 12px; text-align: center; background: #fff;">
                                @if(!empty($general['cafe_logo']->value))
                                <img src="{{ asset('storage/' . $general['cafe_logo']->value) }}"
                                     style="height: 40px; object-fit: contain; margin-bottom: 8px;">
                                <br>
                                @endif
                                <strong>{{ $general['cafe_name']->value ?? 'Nama Cafe' }}</strong><br>
                                {{ $general['cafe_address']->value ?? 'Alamat Cafe' }}<br>
                                {{ $general['cafe_phone']->value ?? 'No. Telepon' }}<br>
                                <hr style="border-top: 1px dashed #ccc;">
                                {{ $receipt['receipt_header']->value ?? 'Header struk' }}<br>
                                <hr style="border-top: 1px dashed #ccc;">
                                Cappuccino x1 ..... Rp 35.000<br>
                                Nasi Goreng x1 .... Rp 25.000<br>
                                <hr style="border-top: 1px dashed #ccc;">
                                Total ............. Rp 60.000<br>
                                Tunai ............. Rp 70.000<br>
                                Kembali ........... Rp 10.000<br>
                                <hr style="border-top: 1px dashed #ccc;">
                                {{ $receipt['receipt_footer']->value ?? 'Footer struk' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card-footer bg-white py-3 px-4">
        <button type="submit" class="btn btn-dark px-4">
            💾 Simpan Pengaturan
        </button>
    </div>
</div>

</form>

<script>
// Toggle QRIS settings saat checkbox berubah
function toggleQris() {
    const checked = document.getElementById('payment_qris').checked;
    document.getElementById('qrisSettings').style.display = checked ? 'block' : 'none';
}

// Pilih tipe QRIS
function selectQrisType(type) {
    document.getElementById('qris_static').checked  = type === 'static';
    document.getElementById('qris_dynamic').checked = type === 'dynamic';

    document.getElementById('qrisStaticSettings').style.display  = type === 'static'  ? 'block' : 'none';
    document.getElementById('qrisDynamicSettings').style.display = type === 'dynamic' ? 'block' : 'none';
}

// Init saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    const qrisType = "{{ $payment['qris_type']->value ?? 'static' }}";
    selectQrisType(qrisType);
});
</script>

@endsection