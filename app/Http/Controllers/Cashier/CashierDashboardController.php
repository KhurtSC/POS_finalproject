<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class CashierDashboardController extends Controller
{
    public function index()
    {
        $products = Product::with('category:id,name')
            ->available()
            ->orderBy('name')
            ->get();

        $categories = Category::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('cashier.dashboard', compact('products', 'categories'));
    }
}