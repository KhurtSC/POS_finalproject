<x-layout title="Sales History">
    <form class="mb-5 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_1fr_1fr_auto]">
        <input type="date" name="from" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
        <input type="date" name="to" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
        <select name="cashier" class="rounded-md border border-slate-300 px-3 py-2 text-sm"><option>All cashiers</option></select>
        <button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white">Filter</button>
    </form>
    <div class="mb-5 flex flex-wrap gap-2">
        @foreach (['PDF', 'XLSX', 'CSV', 'JSON'] as $type)<button class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-bold">Export {{ $type }}</button>@endforeach
    </div>
    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left font-bold text-slate-500"><tr><th class="px-5 py-3">Sale ID</th><th class="px-5 py-3">Cashier</th><th class="px-5 py-3">Items Count</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Date</th><th class="px-5 py-3">Actions</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse (($sales ?? []) as $sale)
                    <tr><td class="px-5 py-4 font-bold">#{{ $sale->id }}</td><td class="px-5 py-4">{{ $sale->cashier->name }}</td><td class="px-5 py-4">{{ $sale->items_count }}</td><td class="px-5 py-4">₱{{ number_format($sale->total, 2) }}</td><td class="px-5 py-4">{{ $sale->created_at->format('M j, Y') }}</td><td class="px-5 py-4"><a class="font-bold text-teal-600" href="{{ route('admin.sales.show', $sale) }}">View Receipt</a></td></tr>
                @empty
                    @for ($i = 2042; $i < 2048; $i++)<tr><td class="px-5 py-4 font-bold">#{{ $i }}</td><td class="px-5 py-4">Demo Cashier</td><td class="px-5 py-4">5</td><td class="px-5 py-4">₱894.00</td><td class="px-5 py-4">{{ now()->format('M j, Y') }}</td><td class="px-5 py-4"><a class="font-bold text-teal-600" href="{{ route('admin.sales.show', $i) }}">View Receipt</a></td></tr>@endfor
                @endforelse
            </tbody>
        </table>
    </section>
</x-layout>
