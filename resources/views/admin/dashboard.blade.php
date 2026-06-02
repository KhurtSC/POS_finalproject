<x-layout title="Admin Dashboard">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-card label="Total Sales Today" value="{{ $salesToday ?? 42 }}" />
        <x-card label="Total Revenue" value="₱{{ number_format($revenue ?? 12840, 2) }}" tone="emerald" />
        <x-card label="Total Products" value="{{ $productsCount ?? 326 }}" tone="amber" />
        <x-card label="Total Users" value="{{ $usersCount ?? 18 }}" tone="sky" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Sales Over 7 Days</h2>
            <canvas id="salesLineChart" class="mt-4 h-72"></canvas>
        </section>
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Top Selling Products</h2>
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
                    <tr><th class="px-5 py-3">Sale ID</th><th class="px-5 py-3">Cashier</th><th class="px-5 py-3">Items</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Date</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse (($recentSales ?? []) as $sale)
                        <tr><td class="px-5 py-4 font-bold">#{{ $sale->id }}</td><td class="px-5 py-4">{{ $sale->cashier->name }}</td><td class="px-5 py-4">{{ $sale->items_count }}</td><td class="px-5 py-4">₱{{ number_format($sale->total, 2) }}</td><td class="px-5 py-4">{{ $sale->created_at->format('M j, Y') }}</td></tr>
                    @empty
                        @for ($i = 1001; $i <= 1010; $i++)
                            <tr><td class="px-5 py-4 font-bold">#{{ $i }}</td><td class="px-5 py-4">Demo Cashier</td><td class="px-5 py-4">{{ rand(2, 8) }}</td><td class="px-5 py-4">₱{{ number_format(rand(2500, 18000), 2) }}</td><td class="px-5 py-4">{{ now()->subDays(1010 - $i)->format('M j, Y') }}</td></tr>
                        @endfor
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @push('scripts')<script src="{{ asset('assets/js/charts.js') }}"></script>@endpush
</x-layout>
