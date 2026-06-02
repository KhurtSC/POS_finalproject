<x-layout title="Reports">
    <form class="mb-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_1fr_auto]">
        <input type="date" name="from" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
        <input type="date" name="to" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
        <button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white">Run Report</button>
    </form>
    <div class="grid gap-4 sm:grid-cols-3">
        <x-card label="Total Sales" value="{{ $totalSales ?? 482 }}" />
        <x-card label="Total Revenue" value="₱{{ number_format($totalRevenue ?? 38240, 2) }}" tone="emerald" />
        <x-card label="Best Selling Product" value="{{ $bestProduct ?? 'Iced Coffee' }}" tone="amber" />
    </div>
    <div class="my-6 flex flex-wrap gap-2">
        @foreach (['PDF', 'XLSX', 'CSV', 'JSON'] as $type)<button class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-bold">Export {{ $type }}</button>@endforeach
    </div>
    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left font-bold text-slate-500"><tr><th class="px-5 py-3">Date</th><th class="px-5 py-3">Transactions</th><th class="px-5 py-3">Revenue</th><th class="px-5 py-3">Top Product</th></tr></thead>
            <tbody class="divide-y divide-slate-100">@for ($i = 0; $i < 7; $i++)<tr><td class="px-5 py-4">{{ now()->subDays($i)->format('M j, Y') }}</td><td class="px-5 py-4">{{ 80 - $i }}</td><td class="px-5 py-4">&#8369;{{ number_format(4250 - ($i * 140), 2) }}</td><td class="px-5 py-4">Iced Coffee</td></tr>@endfor</tbody>
        </table>
    </section>
    <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-black text-slate-950">Import Products</h2>
        <form class="mt-4 flex flex-col gap-3 sm:flex-row"><input type="file" class="rounded-md border border-slate-300 px-3 py-2 text-sm"><button class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white">Upload CSV/XLSX</button></form>
    </section>
</x-layout>
