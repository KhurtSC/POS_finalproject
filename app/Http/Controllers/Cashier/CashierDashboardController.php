<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class CashierDashboardController extends Controller
{
    public function index()
    {
        // We removed the $products query entirely.
        // It will be fetched via JS from the API instead.
        
        $categories = Category::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('cashier.dashboard', compact('categories'));
    }
}