<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cashier\CartController;
use App\Http\Controllers\Cashier\CashierDashboardController;
use Illuminate\Support\Facades\Route;

// ── Auth ─────────────────────────────────────────────────────────────────────

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

// ── Admin ─────────────────────────────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('sales', SaleController::class)->only(['index', 'show']);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// ── Cashier ───────────────────────────────────────────────────────────────────

Route::prefix('cashier')->name('cashier.')->middleware(['auth', 'cashier'])->group(function () {
    Route::get('/dashboard', [CashierDashboardController::class, 'index'])->name('dashboard');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::get('/receipt/{sale}', [CartController::class, 'receipt'])->name('receipt');
});

// ── Internal JSON API (session-authenticated, used by POS frontend) ───────────
// These are web routes (not api.php) so they share the session/cookie auth.
// No token/Sanctum needed — just be logged in.

Route::prefix('api')->name('api.')->middleware('auth')->group(function () {

    // Products API (used by cashier POS grid)
    Route::get('/products',          [ProductApiController::class, 'index'])->name('products.index');
    Route::post('/products',         [ProductApiController::class, 'store'])->name('products.store');
    Route::put('/products/{id}',     [ProductApiController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}',  [ProductApiController::class, 'destroy'])->name('products.destroy');

    // Reports API (JSON endpoint for external consumers or AJAX)
    Route::get('/reports', [ReportApiController::class, 'index'])->name('reports.index');
});
