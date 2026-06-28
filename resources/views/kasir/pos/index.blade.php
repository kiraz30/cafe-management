@extends('layouts.pos')

@section('content')

<div id="posData"
     data-menus='@json($menus->keyBy('id'))'
     data-tax="{{ $settings['tax'] }}"
     data-discount="{{ $settings['discount'] }}">
</div>

{{-- LEFT PANEL --}}
<div class="pos-left">

    <div class="category-tabs">
        <button class="category-tab active" onclick="filterCategory('all', this)">Semua</button>
        @foreach($categories as $cat)
        @if($cat->menus_count > 0)

        @endif
        @endforeach
    </div>

  <div class="menu-grid" id="menuGrid">
    @foreach($menus as $menu)
    <div class="menu-card {{ !$menu->is_available ? 'unavailable' : '' }}"
         data-category="{{ $menu->category_id }}"
         data-menu-id="{{ $menu->id }}"
         data-available="{{ $menu->is_available ? '1' : '0' }}"
         onclick="handleMenuClick(this)">
        <img src="{{ $menu->imageUrl() }}" alt="{{ $menu->name }}">
        <div class="menu-card-body">
            <div class="menu-card-name">{{ $menu->name }}</div>
            <div class="menu-card-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
        </div>
    </div>
    @endforeach
</div>

</div>

{{-- RIGHT PANEL --}}
<div class="pos-right">

    <div class="order-header">
        <div class="order-header-title">🧾 Pesanan Baru</div>
        <div class="order-type-tabs">
            <div class="order-type-tab active" onclick="setOrderType('dine_in', this)">🪑 Dine In</div>
            <div class="order-type-tab" onclick="setOrderType('take_away', this)">🥡 Take Away</div>
        </div>
        <div id="tableSelectWrap">
            <div style="font-size: 12px; color: #888; margin-bottom: 4px;">Pilih Meja (Opsional)</div>
            <select class="table-select" id="tableSelect">
                <option value="">-- Tanpa Meja --</option>
                @foreach($tables as $table)
                <option value="{{ $table->id }}">
                    Meja {{ $table->table_number }} ({{ $table->capacity }} orang)
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="cart-items" id="cartItems">
        <div class="cart-empty" id="cartEmpty">
            <div style="font-size: 40px;">🛒</div>
            <div style="margin-top: 8px;">Belum ada pesanan</div>
            <div style="font-size: 12px; margin-top: 4px;">Klik menu untuk menambahkan</div>
        </div>
    </div>

    <div class="order-notes-wrap">
        <div class="notes-label">Catatan Order</div>
        <textarea class="notes-input" id="orderNotes" rows="2"
                  placeholder="Catatan untuk dapur (opsional)"></textarea>
    </div>

    <div class="order-summary">
        <div class="summary-row">
            <span>Subtotal</span>
            <span id="summarySubtotal">Rp 0</span>
        </div>
        @if($settings['tax'] > 0)
        <div class="summary-row">
            <span>Pajak ({{ $settings['tax'] }}%)</span>
            <span id="summaryTax">Rp 0</span>
        </div>
        @endif
        @if($settings['discount'] > 0)
        <div class="summary-row">
            <span>Diskon ({{ $settings['discount'] }}%)</span>
            <span id="summaryDiscount">- Rp 0</span>
        </div>
        @endif
        <div class="summary-row total">
            <span>Total</span>
            <span id="summaryTotal">Rp 0</span>
        </div>
    </div>

    <button class="pay-btn" id="payBtn" disabled onclick="showPaymentModal()">
        Bayar
    </button>

</div>

{{-- MODAL PILIH PEMBAYARAN --}}
<div class="modal-overlay" id="modalPaymentMethod">
    <div class="modal-box">
        <div class="modal-title">💳 Pilih Metode Pembayaran</div>
        <div class="modal-payment-options">
            @if($settings['payment_cash'] == '1')
            <button class="modal-payment-option active" onclick="selectPaymentMethod('cash', this)">
                💵 Tunai
            </button>
            @endif
            @if($settings['payment_qris'] == '1')
            <button class="modal-payment-option" onclick="selectPaymentMethod('qris', this)">
                📱 QRIS
            </button>
            @endif
        </div>
        <div class="modal-btns">
            <button class="modal-btn modal-btn-cancel" onclick="closeModal('modalPaymentMethod')">Batal</button>
            <button class="modal-btn modal-btn-confirm" onclick="proceedPayment()">Lanjut →</button>
        </div>
    </div>
</div>

{{-- MODAL BAYAR TUNAI --}}
<div class="modal-overlay" id="modalCash">
    <div class="modal-box">
        <div class="modal-title">💵 Pembayaran Tunai</div>
        <div class="modal-total-box">
            <div class="modal-total-label">Total Tagihan</div>
            <div class="modal-total-amount" id="cashTotal">Rp 0</div>
        </div>
        <label class="modal-label">Uang Diterima</label>
        <input type="number" class="modal-input" id="cashReceived"
               placeholder="0" min="0" step="1000" oninput="calcChange()">
        <div class="quick-cash" id="quickCashBtns"></div>
        <div class="modal-change" id="changeWrap" style="display:none">
            <div class="modal-change-label">Kembalian</div>
            <div class="modal-change-amount" id="changeAmount">Rp 0</div>
        </div>
        <div class="modal-btns">
            <button class="modal-btn modal-btn-cancel" onclick="closeModal('modalCash')">Batal</button>
            <button class="modal-btn modal-btn-confirm" id="confirmCashBtn"
                    onclick="confirmPayment('cash')" disabled>✓ Konfirmasi</button>
        </div>
    </div>
</div>

{{-- MODAL BAYAR QRIS --}}
<div class="modal-overlay" id="modalQris">
    <div class="modal-box">
        <div class="modal-title">📱 Pembayaran QRIS</div>
        <div class="modal-total-box" style="text-align:center;">
            <div class="modal-total-label">Total Tagihan</div>
            <div class="modal-total-amount" id="qrisTotal">Rp 0</div>
        </div>
        @if($settings['qris_type'] === 'static' && $settings['qris_image'])
        <div class="qris-image-wrap">
            <img src="{{ asset('storage/' . $settings['qris_image']) }}" alt="QR Code">
            <div class="qris-scan-hint">Scan QR Code untuk membayar</div>
        </div>
        @else
        <div style="text-align:center; padding: 20px; color: #888; font-size: 13px;">
            QR Code belum dikonfigurasi. Silakan upload di Pengaturan.
        </div>
        @endif
        <div class="modal-btns">
            <button class="modal-btn modal-btn-cancel" onclick="closeModal('modalQris')">Batal</button>
            <button class="modal-btn modal-btn-confirm" onclick="confirmPayment('qris')">
                ✓ Sudah Dibayar
            </button>
        </div>
    </div>
</div>

{{-- MODAL SUKSES --}}
<div class="modal-overlay" id="modalSuccess">
    <div class="modal-box">
        <div class="success-icon">✅</div>
        <div class="success-title">Pembayaran Berhasil!</div>
        <div class="success-info" id="successInfo"></div>
        <div class="modal-btns">
            <button class="modal-btn modal-btn-cancel" onclick="newOrder()">+ Order Baru</button>
            <button class="modal-btn modal-btn-confirm" onclick="printReceipt()">🖨️ Cetak Struk</button>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
const posData        = document.getElementById('posData');
const MENUS          = JSON.parse(posData.dataset.menus);
const TAX_PERCENT    = parseFloat(posData.dataset.tax);
const DISCOUNT_PERCENT = parseFloat(posData.dataset.discount);
const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let cart           = {};
let orderType      = 'dine_in';
let paymentMethod  = 'cash';
let currentOrderId = null;

function handleMenuClick(el) {
    const available = el.dataset.available === '1';
    if (!available) return;
    const menuId = String(el.dataset.menuId);
    addToCart(menuId);
}

function addToCart(menuId) {
    menuId = String(menuId); // pastikan tipe sama
    const menu = MENUS[menuId];
    if (!menu) {
        console.log('Menu tidak ditemukan:', menuId, MENUS);
        return;
    }

    if (cart[menuId]) {
        cart[menuId].quantity++;
    } else {
        cart[menuId] = {
            menu_id:  menu.id,
            name:     menu.name,
            price:    parseFloat(menu.price),
            image:    menu.image,
            quantity: 1,
            notes:    '',
        };
    }
    renderCart();
}

function removeFromCart(menuId) {
    if (!cart[menuId]) return;
    cart[menuId].quantity--;
    if (cart[menuId].quantity <= 0) delete cart[menuId];
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const keys      = Object.keys(cart);

    if (keys.length === 0) {
        container.innerHTML = `
            <div class="cart-empty">
                <div style="font-size: 40px;">🛒</div>
                <div style="margin-top: 8px;">Belum ada pesanan</div>
                <div style="font-size: 12px; margin-top: 4px;">Klik menu untuk menambahkan</div>
            </div>`;
        updateSummary();
        return;
    }

    let html = '';
    keys.forEach(menuId => {
        const item   = cart[menuId];
        const sub    = item.price * item.quantity;
        const imgSrc = item.image ? `/storage/${item.image}` : '/images/no-image.png';
        html += `
        <div class="cart-item">
            <img src="${imgSrc}" class="cart-item-img" alt="${item.name}">
            <div class="cart-item-info">
                <div class="cart-item-name">${item.name}</div>
                <div class="cart-item-price">Rp ${fmtNum(item.price)}</div>
            </div>
            <div class="cart-item-controls">
                <button class="qty-btn" onclick="removeFromCart('${menuId}')">−</button>
                <span class="qty-num">${item.quantity}</span>
                <button class="qty-btn" onclick="addToCart('${menuId}')">+</button>
            </div>
            <div class="cart-item-subtotal">Rp ${fmtNum(sub)}</div>
        </div>`;
    });

    container.innerHTML = html;
    updateSummary();
}

function updateSummary() {
    let subtotal = 0;
    Object.values(cart).forEach(i => subtotal += i.price * i.quantity);

    const tax      = subtotal * (TAX_PERCENT / 100);
    const discount = subtotal * (DISCOUNT_PERCENT / 100);
    const total    = subtotal + tax - discount;

    document.getElementById('summarySubtotal').textContent = 'Rp ' + fmtNum(subtotal);
    const taxEl  = document.getElementById('summaryTax');
    const discEl = document.getElementById('summaryDiscount');
    if (taxEl)  taxEl.textContent  = 'Rp ' + fmtNum(tax);
    if (discEl) discEl.textContent = '- Rp ' + fmtNum(discount);
    document.getElementById('summaryTotal').textContent = 'Rp ' + fmtNum(total);

    const payBtn = document.getElementById('payBtn');
    payBtn.disabled    = Object.keys(cart).length === 0;
    payBtn.textContent = Object.keys(cart).length === 0
        ? 'Bayar'
        : `Bayar — Rp ${fmtNum(total)}`;
}

function setOrderType(type, el) {
    orderType = type;
    document.querySelectorAll('.order-type-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('tableSelectWrap').style.display = type === 'dine_in' ? 'block' : 'none';
}

function filterCategory(categoryId, el) {
    document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.menu-card').forEach(card => {
        card.style.display = (categoryId === 'all' || card.dataset.category == categoryId) ? 'block' : 'none';
    });
}

function showPaymentModal() {
    if (Object.keys(cart).length === 0) return;
    openModal('modalPaymentMethod');
}

function selectPaymentMethod(method, el) {
    paymentMethod = method;
    document.querySelectorAll('.modal-payment-option').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

function proceedPayment() {
    closeModal('modalPaymentMethod');
    const total = calcTotal();

    if (paymentMethod === 'cash') {
        document.getElementById('cashTotal').textContent = 'Rp ' + fmtNum(total);
        document.getElementById('cashReceived').value    = '';
        document.getElementById('changeWrap').style.display  = 'none';
        document.getElementById('confirmCashBtn').disabled   = true;

        const quickAmounts = [total, roundUp(total, 50000), roundUp(total, 100000), 200000]
            .filter((v, i, a) => a.indexOf(v) === i);
        document.getElementById('quickCashBtns').innerHTML = quickAmounts
            .map(a => `<button class="quick-cash-btn" onclick="setQuickCash(${a})">Rp ${fmtNum(a)}</button>`)
            .join('');

        openModal('modalCash');
    } else {
        document.getElementById('qrisTotal').textContent = 'Rp ' + fmtNum(total);
        openModal('modalQris');
    }
}

function setQuickCash(amount) {
    document.getElementById('cashReceived').value = amount;
    calcChange();
}

function calcChange() {
    const total    = calcTotal();
    const received = parseFloat(document.getElementById('cashReceived').value) || 0;
    const change   = received - total;

    if (received >= total) {
        document.getElementById('changeWrap').style.display    = 'block';
        document.getElementById('changeAmount').textContent    = 'Rp ' + fmtNum(change);
        document.getElementById('confirmCashBtn').disabled     = false;
    } else {
        document.getElementById('changeWrap').style.display    = 'none';
        document.getElementById('confirmCashBtn').disabled     = true;
    }
}

async function confirmPayment(method) {
    const orderData = {
        order_type: orderType,
        table_id:   document.getElementById('tableSelect')?.value || null,
        notes:      document.getElementById('orderNotes').value,
        items:      Object.values(cart).map(i => ({
            menu_id:  i.menu_id,
            quantity: i.quantity,
            notes:    i.notes,
        })),
    };

    try {
        const orderRes  = await fetch('{{ route("kasir.pos.store") }}', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body:    JSON.stringify(orderData),
        });
        const orderJson = await orderRes.json();
        if (!orderJson.success) { alert('Gagal membuat order: ' + orderJson.message); return; }

        currentOrderId   = orderJson.order_id;
        const amountPaid = method === 'cash'
            ? parseFloat(document.getElementById('cashReceived').value)
            : orderJson.final_amount;

        const payRes  = await fetch(`/kasir/pos/payment/${currentOrderId}`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body:    JSON.stringify({ payment_method: method, amount_paid: amountPaid }),
        });
        const payJson = await payRes.json();
        if (!payJson.success) { alert('Gagal bayar: ' + payJson.message); return; }

        closeModal('modalCash');
        closeModal('modalQris');

        document.getElementById('successInfo').textContent = method === 'cash'
            ? `Kembalian: Rp ${fmtNum(payJson.change)}`
            : 'Pembayaran via QRIS berhasil';
        openModal('modalSuccess');

    } catch (err) {
        alert('Terjadi kesalahan. Coba lagi.');
        console.error(err);
    }
}

function newOrder() {
    cart = {};
    currentOrderId = null;
    renderCart();
    closeModal('modalSuccess');
    document.getElementById('orderNotes').value  = '';
    document.getElementById('tableSelect').value = '';
}

function printReceipt() {
    if (currentOrderId) window.open(`/kasir/pos/receipt/${currentOrderId}`, '_blank');
    newOrder();
}

function calcTotal() {
    let subtotal = 0;
    Object.values(cart).forEach(i => subtotal += i.price * i.quantity);
    const tax      = subtotal * (TAX_PERCENT / 100);
    const discount = subtotal * (DISCOUNT_PERCENT / 100);
    return subtotal + tax - discount;
}

function fmtNum(n)           { return Math.round(n).toLocaleString('id-ID'); }
function roundUp(amount, to) { return Math.ceil(amount / to) * to; }
function openModal(id)       { document.getElementById(id).classList.add('show'); }
function closeModal(id)      { document.getElementById(id).classList.remove('show'); }
</script>

@endsection