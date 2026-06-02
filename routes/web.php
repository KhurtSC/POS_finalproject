<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cashier\CartController;
use App\Http\Controllers\Cashier\CashierDashboardController;
use Illuminate\Support\Facades\Route;

// ── Auth ─────────────────────────────────────────────────────────────────────

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Registration is restricted to logged-in admins only (P2.6)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// ── Admin ─────────────────────────────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('sales', SaleController::class)->only(['index', 'show']);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Export routes (P2.1 / P2.2 / P2.3)
    Route::get('/reports/export/csv',  [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export/xlsx', [ReportController::class, 'exportXlsx'])->name('reports.export.xlsx');
    Route::get('/reports/export/pdf',  [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    // Product import (P2.4)
    Route::get('/products/import',  [ProductController::class, 'importForm'])->name('products.import.form');
    Route::post('/products/import', [ProductController::class, 'importCsv'])->name('products.import.csv');

    // Activity logs (P3.2)
    Route::get('/logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('logs.index');
});

// ── Cashier ───────────────────────────────────────────────────────────────────

Route::prefix('cashier')->name('cashier.')->middleware(['auth', 'cashier'])->group(function () {
    Route::get('/dashboard', [CashierDashboardController::class, 'index'])->name('dashboard');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::get('/receipt/{sale}', [CartController::class, 'receipt'])->name('receipt');
});

// NOTE: All JSON API routes live in routes/api.php (loaded automatically under /api prefix).
// The SaleApiController::void() route is registered there and is accessible from the UI
// via the void button added to admin/sales/show.blade.php.