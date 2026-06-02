<?php

use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\SaleApiController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductApiController::class, 'index']);
Route::post('/products', [ProductApiController::class, 'store']);
Route::put('/products/{id}', [ProductApiController::class, 'update']);
Route::delete('/products/{id}', [ProductApiController::class, 'destroy']);

Route::post('/sales', [SaleApiController::class, 'store']);
Route::get('/sales', [SaleApiController::class, 'index']);
Route::get('/sales/{id}', [SaleApiController::class, 'show']);

Route::get('/reports', [ReportApiController::class, 'index']);
