<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index() { return view('cashier.cart'); }
    public function receipt($sale) { return view('cashier.receipt', ['sale' => null]); }
}
