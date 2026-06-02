<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->filled('from') ? $request->from : now()->startOfMonth()->toDateString();
        $to   = $request->filled('to')   ? $request->to   : now()->toDateString();

        [$totalSales, $totalRevenue, $topProducts, $dailyBreakdown] = $this->buildReportData($from, $to);

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

    // ── P2.1 — CSV Export ─────────────────────────────────────────────────────

    public function exportCsv(Request $request): StreamedResponse
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->toDateString());

        [$totalSales, $totalRevenue, $topProducts, $dailyBreakdown] = $this->buildReportData($from, $to);

        $filename = "report_{$from}_to_{$to}.csv";

        return response()->streamDownload(function () use ($from, $to, $totalSales, $totalRevenue, $topProducts, $dailyBreakdown) {
            $handle = fopen('php://output', 'w');

            // Summary section
            fputcsv($handle, ['PointSale — Sales Report']);
            fputcsv($handle, ['Period', "{$from} to {$to}"]);
            fputcsv($handle, ['Total Transactions', $totalSales]);
            fputcsv($handle, ['Total Revenue', number_format($totalRevenue, 2)]);
            fputcsv($handle, []);

            // Top products
            fputcsv($handle, ['Top 5 Products']);
            fputcsv($handle, ['Rank', 'Product', 'Units Sold', 'Revenue']);
            foreach ($topProducts as $i => $item) {
                fputcsv($handle, [
                    $i + 1,
                    $item->product_name,
                    $item->total_qty,
                    number_format($item->total_revenue, 2),
                ]);
            }
            fputcsv($handle, []);

            // Daily breakdown
            fputcsv($handle, ['Daily Breakdown']);
            fputcsv($handle, ['Date', 'Transactions', 'Revenue']);
            foreach ($dailyBreakdown as $row) {
                fputcsv($handle, [
                    $row->date,
                    $row->transactions,
                    number_format($row->revenue, 2),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    // ── P2.2 — XLSX Export ────────────────────────────────────────────────────
    // Requires: composer require maatwebsite/excel
    // If the package is not installed, this falls back to CSV download.

    public function exportXlsx(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->toDateString());

        if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            // Graceful fallback if package not installed
            return redirect()->route('admin.reports.export.csv', ['from' => $from, 'to' => $to])
                ->with('warning', 'Excel export requires maatwebsite/excel. Falling back to CSV.');
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReportExport($from, $to),
            "report_{$from}_to_{$to}.xlsx"
        );
    }

    // ── P2.3 — PDF Export ─────────────────────────────────────────────────────
    // Requires: composer require barryvdh/laravel-dompdf

    public function exportPdf(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->toDateString());

        [$totalSales, $totalRevenue, $topProducts, $dailyBreakdown] = $this->buildReportData($from, $to);
        $bestProduct = $topProducts->first()?->product_name ?? '—';

        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return redirect()->route('admin.reports.export.csv', ['from' => $from, 'to' => $to])
                ->with('warning', 'PDF export requires barryvdh/laravel-dompdf. Falling back to CSV.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf', compact(
            'totalSales', 'totalRevenue', 'topProducts', 'dailyBreakdown', 'bestProduct', 'from', 'to'
        ));

        return $pdf->download("report_{$from}_to_{$to}.pdf");
    }

    // ── Shared query logic ────────────────────────────────────────────────────

    private function buildReportData(string $from, string $to): array
    {
        $salesQuery = Sale::completed()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        $totalSales   = (clone $salesQuery)->count();
        $totalRevenue = (clone $salesQuery)->sum('total_amount');

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

        $dailyBreakdown = Sale::completed()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as transactions, SUM(total_amount) as revenue')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date', 'desc')
            ->get();

        return [$totalSales, $totalRevenue, $topProducts, $dailyBreakdown];
    }
}