<x-layout title="Sales History">

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.sales.index') }}"
          class="mb-5 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm
                 md:grid-cols-[1fr_1fr_1fr_1fr_auto]">
        <input type="date" name="from" value="{{ request('from') }}"
               class="rounded-md border border-slate-300 px-3 py-2 text-sm">
        <input type="date" name="to"   value="{{ request('to') }}"
               class="rounded-md border border-slate-300 px-3 py-2 text-sm">

        <select name="cashier" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">All cashiers</option>
            @foreach ($cashiers as $cashier)
                <option value="{{ $cashier->id }}" @selected(request('cashier') == $cashier->id)>
                    {{ $cashier->name }}
                </option>
            @endforeach
        </select>

        <select name="status" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">All statuses</option>
            <option value="completed" @selected(request('status') === 'completed')>Completed</option>
            <option value="voided"    @selected(request('status') === 'voided')>Voided</option>
        </select>

        <button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white">
            Filter
        </button>
    </form>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-4 rounded-md bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left font-bold text-slate-500">
                <tr>
                    <th class="px-5 py-3">Reference</th>
                    <th class="px-5 py-3">Cashier</th>
                    <th class="px-5 py-3">Items</th>
                    <th class="px-5 py-3">Total</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($sales as $sale)
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
                        <td class="px-5 py-4">
                            <a class="font-bold text-teal-600 hover:underline"
                               href="{{ route('admin.sales.show', $sale) }}">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                            No sales found for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div class="mt-4">
        {{ $sales->links() }}
    </div>
</x-layout>