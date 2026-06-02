<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SaleController extends Controller
{
    public function index() { return view('admin.sales.index'); }
    public function show($sale) { return view('admin.sales.show', ['sale' => null]); }
}
