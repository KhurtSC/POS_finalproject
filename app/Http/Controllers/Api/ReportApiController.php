<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    /**
     * GET /api/reports
     *
     * Query params:
     *   start_date  (Y-m-d, default: start of current month)
     *   end_date    (Y-m-d, default: today)
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $from = $request->filled('start_date')
            ? $request->start_date
            : now()->startOfMonth()->toDateString();

        $to = $request->filled('end_date')
            ? $request->end_date
            : now()->toDateString();

        // Base query: completed sales within range
        $baseQuery = Sale::completed()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        $totalRevenue = (clone $baseQuery)->sum('total_amount');
        $totalSales   = (clone $baseQuery)->count();

        // Top-selling products (by quantity sold)
        $topProducts = SaleItem::selectRaw(
                'product_id,
                 product_name,
                 product_sku,
                 SUM(quantity)  AS total_qty,
                 SUM(subtotal)  AS total_revenue'
            )
            ->whereHas('sale', fn ($q) =>
                $q->completed()
                  ->whereDate('created_at', '>=', $from)
                  ->whereDate('created_at', '<=', $to)
            )
            ->groupBy('product_id', 'product_name', 'product_sku')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Revenue breakdown per day
        $dailyRevenue = Sale::completed()
            ->selectRaw(
                'DATE(created_at) AS date,
                 COUNT(*)         AS sales_count,
                 SUM(total_amount) AS revenue'
            )
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();

        return response()->json([
            'period' => [
                'start_date' => $from,
                'end_date'   => $to,
            ],
            'summary' => [
                'total_revenue' => (float) $totalRevenue,
                'total_sales'   => $totalSales,
            ],
            'top_products'    => $topProducts,
            'daily_breakdown' => $dailyRevenue,
        ]);
    }
}