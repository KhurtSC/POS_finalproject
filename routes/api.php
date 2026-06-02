<?php

use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\SaleApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes below require an authenticated session (web guard) so that
| the same cookie-based auth used by the Blade views protects the API.
| The POS frontend is served from the same origin, so this is the right
| guard to use — no token management needed on the client side.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // ── Products ─────────────────────────────────────────────────────────────
    Route::get('/products',         [ProductApiController::class, 'index']);
    Route::post('/products',        [ProductApiController::class, 'store'])->middleware('admin');
    Route::put('/products/{id}',    [ProductApiController::class, 'update'])->middleware('admin');
    Route::delete('/products/{id}', [ProductApiController::class, 'destroy'])->middleware('admin');

    // ── Sales ─────────────────────────────────────────────────────────────────
    Route::post('/sales',           [SaleApiController::class, 'store'])->middleware('cashier');
    Route::get('/sales',            [SaleApiController::class, 'index'])->middleware('admin');
    Route::get('/sales/{id}',       [SaleApiController::class, 'show']);
    Route::post('/sales/{id}/void', [SaleApiController::class, 'void'])->middleware('admin');

    // ── Reports ───────────────────────────────────────────────────────────────
    Route::get('/reports',          [ReportApiController::class, 'index'])->middleware('admin');
});