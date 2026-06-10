@extends('layouts.app')
@section('page-title', 'Buat Pembelian')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="page-header">
    <h5>Buat Pembelian Baru</h5>
    <p>Input pembelian bahan baku dari supplier</p>
</div>

<form method="POST" action="{{ route('admin.purchases.store') }}" id="purchaseForm">
@csrf
<div class="row g-4">

    {{-- Info Pembelian --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <span style="font-size: 14px; font-weight: 600;">Info Pembelian</span>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date"
                           class="form-control @error('purchase_date') is-invalid @enderror"
                           value="{{ old('purchase_date', date('Y-m-d')) }}">
                    @error('purchase_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="received" {{ old('status') == 'received' ? 'selected' : '' }}>Diterima</option>
                        <option value="pending"  {{ old('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span style="font-size: 13px; font-weight: 600;">Total Pembelian</span>
                    <span style="font-size: 18px; font-weight: 700;" id="grandTotal">Rp 0</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Item Pembelian --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span style="font-size: 14px; font-weight: 600;">Item Pembelian</span>
                <button type="button" class="btn btn-outline-dark btn-sm" onclick="addItem()">
                    + Tambah Item
                </button>
            </div>
            <div class="card-body p-4">

                @error('items')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div id="itemsContainer">
                    {{-- Item rows akan ditambah via JS --}}
                </div>

                <div class="text-center py-3" id="emptyMsg" style="color: #888; font-size: 13px;">
                    Klik "+ Tambah Item" untuk menambah bahan baku
                </div>

            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-dark px-4">Simpan Pembelian</button>
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
        </div>
    </div>

</div>
</form>

<script>
const ingredients = @json($ingredients);
let itemCount = 0;

function addItem() {
    document.getElementById('emptyMsg').style.display = 'none';
    const container = document.getElementById('itemsContainer');
    const index = itemCount++;

    const options = ingredients.map(i =>
        `<option value="${i.id}">${i.name} (${i.unit})</option>`
    ).join('');

    const row = document.createElement('div');
    row.className = 'row g-2 mb-3 align-items-end item-row';
    row.id = `item-${index}`;
    row.innerHTML = `
        <div class="col-md-5">
            <label class="form-label" style="font-size:12px;">Bahan Baku</label>
            <select name="items[${index}][ingredient_id]" class="form-select form-select-sm" required>
                <option value="">-- Pilih Bahan --</option>
                ${options}
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:12px;">Jumlah</label>
            <input type="number" name="items[${index}][quantity]"
                   class="form-control form-control-sm qty-input"
                   placeholder="0" min="0.01" step="0.01"
                   oninput="calcSubtotal(${index})" required>
        </div>
        <div class="col-md-3">
            <label class="form-label" style="font-size:12px;">Harga Satuan</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text">Rp</span>
                <input type="number" name="items[${index}][unit_price]"
                       class="form-control price-input"
                       placeholder="0" min="0" step="100"
                       oninput="calcSubtotal(${index})" required>
            </div>
        </div>
        <div class="col-md-1 text-center">
            <label class="form-label" style="font-size:12px;">Subtotal</label>
            <div id="subtotal-${index}" style="font-size:12px;font-weight:600;color:#2c3e50;">Rp 0</div>
        </div>
        <div class="col-md-1 text-center">
            <button type="button" class="btn btn-outline-danger btn-sm"
                    onclick="removeItem(${index})">✕</button>
        </div>
    `;
    container.appendChild(row);
}

function removeItem(index) {
    document.getElementById(`item-${index}`).remove();
    calcGrandTotal();
    if (document.querySelectorAll('.item-row').length === 0) {
        document.getElementById('emptyMsg').style.display = 'block';
    }
}

function calcSubtotal(index) {
    const row = document.getElementById(`item-${index}`);
    const qty   = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const sub   = qty * price;
    document.getElementById(`subtotal-${index}`).textContent =
        'Rp ' + sub.toLocaleString('id-ID');
    calcGrandTotal();
}

function calcGrandTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        total += qty * price;
    });
    document.getElementById('grandTotal').textContent =
        'Rp ' + total.toLocaleString('id-ID');
}
</script>

@endsection