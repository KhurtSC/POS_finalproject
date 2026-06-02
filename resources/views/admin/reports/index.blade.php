<x-layout title="Reports">

    {{-- Date range filter --}}
    <form method="GET" action="{{ route('admin.reports.index') }}"
          class="mb-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm
                 md:grid-cols-[1fr_1fr_auto]">
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}"
                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}"
                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div class="flex items-end">
            <button class="w-full rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">
                Run Report
            </button>
        </div>
    </form>

    {{-- Summary cards --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <x-card label="Total Sales"          value="{{ $totalSales }}" />
        <x-card label="Total Revenue"        value="₱{{ number_format($totalRevenue, 2) }}" tone="emerald" />
        <x-card label="Best Selling Product" value="{{ $bestProduct }}" tone="amber" />
    </div>

    {{-- Top Products --}}
    @if ($topProducts->isNotEmpty())
    <section class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="font-black text-slate-950">Top 5 Products</h2>
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left font-bold text-slate-500">
                <tr>
                    <th class="px-5 py-3">#</th>
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3 text-right">Units Sold</th>
                    <th class="px-5 py-3 text-right">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($topProducts as $i => $item)
                    <tr>
                        <td class="px-5 py-4 font-bold text-slate-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-4 font-medium">{{ $item->product_name }}</td>
                        <td class="px-5 py-4 text-right">{{ number_format($item->total_qty) }}</td>
                        <td class="px-5 py-4 text-right">₱{{ number_format($item->total_revenue, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    @endif

    {{-- Daily Breakdown --}}
    <section class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="font-black text-slate-950">Daily Breakdown</h2>
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left font-bold text-slate-500">
                <tr>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3 text-right">Transactions</th>
                    <th class="px-5 py-3 text-right">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($dailyBreakdown as $row)
                    <tr>
                        <td class="px-5 py-4">
                            {{ \Carbon\Carbon::parse($row->date)->format('M j, Y') }}
                        </td>
                        <td class="px-5 py-4 text-right">{{ number_format($row->transactions) }}</td>
                        <td class="px-5 py-4 text-right">₱{{ number_format($row->revenue, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-slate-400">
                            No sales data for the selected period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

</x-layout>
