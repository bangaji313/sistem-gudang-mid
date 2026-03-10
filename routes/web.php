<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\TelegramSetupController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

// ============================================================
// Telegram Webhook (public — tanpa auth & CSRF)
// ============================================================
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook');
Route::post('/api/telegram/webhook', [TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook.api');

// Telegram Setup — hanya untuk development/setup awal
Route::get('/telegram/set-webhook', [TelegramSetupController::class, 'setWebhook'])->name('telegram.set-webhook');
Route::get('/telegram/remove-webhook', [TelegramSetupController::class, 'removeWebhook'])->name('telegram.remove-webhook');
Route::get('/telegram/webhook-info', [TelegramSetupController::class, 'webhookInfo'])->name('telegram.webhook-info');

// ============================================================
// Rute Guest (belum login)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// ============================================================
// Rute Auth (harus login)
// ============================================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ---------------------------------------------------------
    // Rute untuk role: super_admin
    // ---------------------------------------------------------
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });

    // ---------------------------------------------------------
    // Master Data: CRUD penuh untuk purchase & super_admin
    // ---------------------------------------------------------
    Route::middleware('role:purchase,super_admin')->group(function () {
        Route::get('/vendors/export-pdf', [VendorController::class, 'exportPdf'])->name('vendors.export-pdf');
        Route::resource('vendors', VendorController::class);
        Route::get('/divisis/export-pdf', [DivisiController::class, 'exportPdf'])->name('divisis.export-pdf');
        Route::resource('divisis', DivisiController::class);
    });

    // ---------------------------------------------------------
    // Master Barang: READ untuk purchase, super_admin & gudang
    // ---------------------------------------------------------
    Route::middleware('role:purchase,super_admin,gudang')->group(function () {
        Route::get('/items', [ItemController::class, 'index'])->name('items.index');
        Route::get('/items/export-excel', [ItemController::class, 'exportExcel'])->name('items.export-excel');
        Route::get('/items/export-pdf', [ItemController::class, 'exportPdf'])->name('items.export-pdf');
        Route::get('/items/{item}/print-qr', [ItemController::class, 'printQr'])->name('items.print-qr');
    });

    // ---------------------------------------------------------
    // Master Barang: CUD (Create/Update/Delete) hanya purchase & super_admin
    // ---------------------------------------------------------
    Route::middleware('role:purchase,super_admin')->group(function () {
        Route::resource('items', ItemController::class)->except(['index', 'show']);
    });

    // ---------------------------------------------------------
    // Purchase Orders: purchase & super_admin
    // ---------------------------------------------------------
    Route::middleware('role:purchase,super_admin')->group(function () {
        Route::get('/purchase-orders/export-pdf', [PurchaseOrderController::class, 'exportPdf'])->name('purchase-orders.export-pdf');
        Route::get('/purchase-orders/export-excel', [PurchaseOrderController::class, 'exportExcel'])->name('purchase-orders.export-excel');
        Route::get('/purchase-orders/{purchase_order}/print-continuous', [PurchaseOrderController::class, 'printContinuousForm'])->name('purchase-orders.print-continuous');
        Route::get('/purchase-orders/{purchase_order}/print-excel', [PurchaseOrderController::class, 'printExcelMatrix'])->name('purchase-orders.print-excel');
        Route::resource('purchase-orders', PurchaseOrderController::class)->only(['index', 'create', 'store', 'show']);
        Route::patch('/purchase-orders/{purchase_order}/arrived', [PurchaseOrderController::class, 'markAsArrived'])
            ->name('purchase-orders.arrived');
        Route::patch('/purchase-orders/{purchase_order}/lunas', [PurchaseOrderController::class, 'markAsLunas'])
            ->name('purchase-orders.lunas');
        Route::patch('/purchase-orders/{purchase_order}/cancel', [PurchaseOrderController::class, 'markAsCanceled'])
            ->name('purchase-orders.cancel');
    });

    // ---------------------------------------------------------
    // Transaksi Gudang: gudang, purchase & super_admin
    // ---------------------------------------------------------
    Route::middleware('role:gudang,purchase,super_admin')->group(function () {
        Route::get('/transactions/export-excel', [TransactionController::class, 'exportExcel'])->name('transactions.export-excel');
        Route::get('/transactions/export-pdf', [TransactionController::class, 'exportPdf'])->name('transactions.export-pdf');
        Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store']);
    });

    // ---------------------------------------------------------
    // Profil Pengguna: semua role yang sedang login
    // ---------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ---------------------------------------------------------
    // Manajemen User: super_admin & admin
    // ---------------------------------------------------------
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // ---------------------------------------------------------
    // Master Data: READ-ONLY untuk gudang
    // ---------------------------------------------------------
    Route::middleware('role:gudang')->group(function () {
        Route::get('/master/vendors', [VendorController::class, 'index'])->name('vendors.index.gudang');
        Route::get('/master/divisis', [DivisiController::class, 'index'])->name('divisis.index.gudang');
    });
});

// ============================================================
// Redirect root ke login
// ============================================================
Route::get('/', function () {
    return redirect('/login');
});
