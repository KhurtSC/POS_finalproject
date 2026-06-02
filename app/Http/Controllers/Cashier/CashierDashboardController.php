<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;

class CashierDashboardController extends Controller
{
    public function index() { return view('cashier.dashboard'); }
}
