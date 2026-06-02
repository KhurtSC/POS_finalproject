<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Sale;

class CartController extends Controller
{
    public function index()
    {
        return view('cashier.cart');
    }

    public function receipt(Sale $sale)
    {
        // Eager-load everything the receipt view needs
        $sale->load(['items', 'cashier:id,name', 'voidedBy:id,name']);

        return view('cashier.receipt', compact('sale'));
    }
}