<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $cashiers = User::where('role', 'cashier')->orderBy('name')->get(['id', 'name']);

        $sales = Sale::with(['cashier:id,name'])
            ->withCount('items')
            ->when($request->filled('from'), fn ($q) =>
                $q->whereDate('created_at', '>=', $request->from)
            )
            ->when($request->filled('to'), fn ($q) =>
                $q->whereDate('created_at', '<=', $request->to)
            )
            ->when($request->filled('cashier'), fn ($q) =>
                $q->forCashier((int) $request->cashier)
            )
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.sales.index', compact('sales', 'cashiers'));
    }

    public function show(Sale $sale): View
    {
        $sale->load([
            'cashier:id,name,email',
            'voidedBy:id,name',
            'items.product:id,name,sku,image',
        ]);

        return view('admin.sales.show', compact('sale'));
    }
}