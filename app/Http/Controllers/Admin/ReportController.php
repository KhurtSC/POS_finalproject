<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->filled('from') ? $request->from : now()->startOfMonth()->toDateString();
        $to   = $request->filled('to')   ? $request->to   : now()->toDateString();

        $salesQuery = Sale::completed()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        $totalSales   = (clone $salesQuery)->count();
        $totalRevenue = (clone $salesQuery)->sum('total_amount');

        // Top 5 best-selling products in range
        $topProducts = SaleItem::selectRaw('product_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->whereHas('sale', fn ($q) =>
                $q->completed()
                  ->whereDate('created_at', '>=', $from)
                  ->whereDate('created_at', '<=', $to)
            )
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Daily breakdown
        $dailyBreakdown = Sale::completed()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as transactions, SUM(total_amount) as revenue')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date', 'desc')
            ->get();

        $bestProduct = $topProducts->first()?->product_name ?? '—';

        return view('admin.reports.index', compact(
            'totalSales',
            'totalRevenue',
            'topProducts',
            'dailyBreakdown',
            'bestProduct',
            'from',
            'to',
        ));
    }
}