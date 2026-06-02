<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers    = User::count();
        $totalProducts = Product::count();
        $totalSales    = Sale::count();
        $totalRevenue  = Sale::completed()->sum('total_amount');

        $salesToday = Sale::completed()
            ->forDate(today()->toDateString())
            ->count();

        $recentSales = Sale::with(['cashier:id,name'])
            ->withCount('items')
            ->latest()
            ->limit(10)
            ->get();

        // Revenue for the last 7 days (for chart)
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->toDateString();
            return [
                'date'    => $date,
                'label'   => now()->subDays($daysAgo)->format('M j'),
                'revenue' => (float) Sale::completed()->forDate($date)->sum('total_amount'),
                'count'   => Sale::completed()->forDate($date)->count(),
            ];
        });

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProducts',
            'totalSales',
            'totalRevenue',
            'salesToday',
            'recentSales',
            'last7Days',
        ));
    }
}