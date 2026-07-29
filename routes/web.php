<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboard;
use App\Http\Controllers\Kasir\PosController;
use App\Http\Controllers\Barista\DashboardController as BaristaDashboard;
use App\Http\Controllers\Pelayan\DashboardController as PelayanDashboard;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Kasir\OrderController as KasirOrderController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    // Shift
    Route::resource('shifts', ShiftController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('ingredients', IngredientController::class);
    Route::post('ingredients/{ingredient}/adjust-stock', [IngredientController::class, 'adjustStock'])->name('ingredients.adjust-stock');
    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('menus', MenuController::class)->except(['show']);
    Route::resource('tables', TableController::class)->except(['show']);
    Route::patch('tables/{table}/status', [TableController::class, 'updateStatus'])->name('tables.update-status');
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

     // Order
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::delete('orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
});


// Kasir routes
    Route::middleware(['auth', 'role:kasir,admin'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirDashboard::class, 'index'])->name('dashboard');

    // POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/order', [PosController::class, 'store'])->name('pos.store');
    Route::post('/pos/payment/{order}', [PosController::class, 'payment'])->name('pos.payment');
    Route::get('/pos/receipt/{order}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::post('/pos/cancel/{order}', [PosController::class, 'cancel'])->name('pos.cancel');
    Route::get('/pos/kitchen-receipt/{order}', [PosController::class, 'kitchenReceipt'])->name('pos.kitchen-receipt');

    //bn Meja dari POS
    Route::get('/pos/tables', [PosController::class, 'tables'])->name('pos.tables');
    Route::post('/pos/tables/{table}', [PosController::class, 'updateTable'])->name('pos.tables.update');


    // Order History
    Route::get('/orders', [KasirOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [KasirOrderController::class, 'show'])->name('orders.show');
});

// Barista routes
Route::middleware(['auth', 'role:barista'])->prefix('barista')->name('barista.')->group(function () {
    Route::get('/dashboard', [BaristaDashboard::class, 'index'])->name('dashboard');
    Route::post('/orders/{order}/status', [BaristaDashboard::class,'updateStatus'])->name('orders.update-status');
});

// Pelayan routes
Route::middleware(['auth', 'role:pelayan'])->prefix('pelayan')->name('pelayan.')->group(function () {
    Route::get('/dashboard', [PelayanDashboard::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';