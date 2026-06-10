@extends('layouts.app')

@section('page-title', 'Dashboard Admin')

@section('content')

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted" style="font-size: 13px;">Total User</div>
                        <div class="fs-4 fw-bold">0</div>
                    </div>
                    <div style="font-size: 32px;">👥</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted" style="font-size: 13px;">Total Menu</div>
                        <div class="fs-4 fw-bold">0</div>
                    </div>
                    <div style="font-size: 32px;">🍽️</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted" style="font-size: 13px;">Pesanan Hari Ini</div>
                        <div class="fs-4 fw-bold">0</div>
                    </div>
                    <div style="font-size: 32px;">🧾</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted" style="font-size: 13px;">Pendapatan Hari Ini</div>
                        <div class="fs-4 fw-bold">Rp 0</div>
                    </div>
                    <div style="font-size: 32px;">💰</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection