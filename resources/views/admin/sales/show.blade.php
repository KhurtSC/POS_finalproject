<x-layout title="Sale #{{ $sale->reference }}">

    <div class="mx-auto max-w-3xl">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-950">{{ $sale->reference }}</h1>
                <p class="text-sm text-slate-500">{{ $sale->created_at->format('F j, Y g:i A') }}</p>
            </div>
            <a href="{{ route('admin.sales.index') }}"
               class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold hover:bg-slate-50">
                ← Back
            </a>
        </div>

        {{-- Status badge --}}
        <div class="mb-6 rounded-lg border p-4
            {{ $sale->status === 'completed'
                ? 'border-emerald-200 bg-emerald-50'
                : 'border-red-200 bg-red-50' }}">
            <span class="font-bold
                {{ $sale->status === 'completed' ? 'text-emerald-700' : 'text-red-700' }}">
                Status: {{ ucfirst($sale->status) }}
            </span>
            @if ($sale->isVoided())
                <p class="mt-1 text-sm text-red-600">
                    Voided by {{ $sale->voidedBy->name ?? '—' }}
                    on {{ $sale->voided_at?->format('M j, Y g:i A') }}
                </p>
                @if ($sale->void_reason)
                    <p class="mt-1 text-sm text-red-600">Reason: {{ $sale->void_reason }}</p>
                @endif
            @endif
        </div>

        {{-- Sale meta --}}
        <div class="mb-6 grid gap-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-2">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Cashier</p>
                <p class="mt-1 font-medium text-slate-900">{{ $sale->cashier->name ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $sale->cashier->email ?? '' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Payment Method</p>
                <p class="mt-1 font-medium capitalize text-slate-900">{{ $sale->payment_method }}</p>
            </div>
            @if ($sale->notes)
            <div class="sm:col-span-2">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Notes</p>
                <p class="mt-1 text-sm text-slate-700">{{ $sale->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Line items --}}
        <section class="mb-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="font-black text-slate-950">Items</h2>
            </div>
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left font-bold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Product</th>
                        <th class="px-5 py-3">SKU</th>
                        <th class="px-5 py-3 text-right">Unit Price</th>
                        <th class="px-5 py-3 text-right">Qty</th>
                        <th class="px-5 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($sale->items as $item)
                        <tr>
                            <td class="px-5 py-4 font-medium">{{ $item->product_name }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $item->product_sku ?? '—' }}</td>
                            <td class="px-5 py-4 text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-5 py-4 text-right">{{ $item->quantity }}</td>
                            <td class="px-5 py-4 text-right font-medium">₱{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        {{-- Totals --}}
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Subtotal</span>
                    <span>₱{{ number_format($sale->subtotal, 2) }}</span>
                </div>
                @if ($sale->discount_amount > 0)
                <div class="flex justify-between text-red-600">
                    <span>Discount
                        @if ($sale->discount_percent > 0)
                            ({{ $sale->discount_percent }}%)
                        @endif
                    </span>
                    <span>− ₱{{ number_format($sale->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-black text-slate-950">
                    <span>Total</span>
                    <span>₱{{ number_format($sale->total_amount, 2) }}</span>
                </div>
                @if ($sale->payment_method === 'cash')
                <div class="flex justify-between text-slate-500">
                    <span>Cash tendered</span>
                    <span>₱{{ number_format($sale->amount_tendered, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Change</span>
                    <span>₱{{ number_format($sale->change_amount, 2) }}</span>
                </div>
                @endif
            </div>
        </div>

    </div>
</x-layout>