<x-layout title="Admin Dashboard">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-card label="Total Sales Today"  value="{{ $salesToday }}" />
        <x-card label="Total Revenue"      value="₱{{ number_format($totalRevenue, 2) }}" tone="emerald" />
        <x-card label="Total Products"     value="{{ $totalProducts }}" tone="amber" />
        <x-card label="Total Users"        value="{{ $totalUsers }}" tone="sky" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Sales Over 7 Days</h2>
            <canvas id="salesLineChart" class="mt-4 h-72"></canvas>
        </section>
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Transactions Over 7 Days</h2>
            <canvas id="topProductsChart" class="mt-4 h-72"></canvas>
        </section>
    </div>

    <section class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-lg font-black text-slate-950">Recent Sales</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left font-bold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Reference</th>
                        <th class="px-5 py-3">Cashier</th>
                        <th class="px-5 py-3">Items</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentSales as $sale)
                        <tr>
                            <td class="px-5 py-4 font-bold">{{ $sale->reference }}</td>
                            <td class="px-5 py-4">{{ $sale->cashier->name ?? '—' }}</td>
                            <td class="px-5 py-4">{{ $sale->items_count }}</td>
                            <td class="px-5 py-4">₱{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2 py-0.5 text-xs font-bold
                                    {{ $sale->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">{{ $sale->created_at->format('M j, Y g:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">No sales recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @push('scripts')
    <script>
        window.__chartData = {
            labels:  @json($last7Days->pluck('label')),
            revenue: @json($last7Days->pluck('revenue')),
            counts:  @json($last7Days->pluck('count')),
        };
    </script>
    <script src="{{ asset('assets/js/charts.js') }}"></script>
    @endpush
</x-layout>
