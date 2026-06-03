<section class="mx-auto max-w-md rounded-lg border border-slate-200 bg-white p-6 shadow-sm print:border-0 print:shadow-none">
    <div class="text-center">
        <img src="{{ asset('assets/images/brand/pointsale-logo.svg') }}" alt="Cafe POS" class="mx-auto mb-3 h-14 w-auto rounded-lg print:h-10">
        <h1 class="text-xl font-black text-slate-950">Cafe POS Receipt</h1>
        <p class="text-sm font-medium text-slate-500">{{ $sale->reference }}</p>
        <p class="text-sm font-medium text-slate-500">{{ $sale->created_at->format('M j, Y g:i A') }}</p>
        <p class="text-sm font-medium text-slate-500">Cashier: {{ $sale->cashier->name }}</p>
    </div>

    {{-- Items --}}
    <div class="my-6 divide-y divide-slate-100 border-y border-slate-200">
        @foreach ($sale->items as $item)
            <div class="flex justify-between py-3 text-sm">
                <span>{{ $item->product_name }} &times; {{ $item->quantity }}</span>
                <span>&#8369;{{ number_format($item->subtotal, 2) }}</span>
            </div>
        @endforeach
    </div>

    {{-- Totals --}}
    @php
        // VAT-inclusive pricing (Philippine standard under TRAIN Law).
        // total_amount already contains VAT — we back-calculate the breakdown.
        $vatBase    = $sale->total_amount;          // VAT-inclusive total after discount
        $vatAmount  = round($vatBase * 12 / 112, 2);
        $vatExcl    = round($vatBase - $vatAmount, 2);
    @endphp

    <div class="space-y-1 text-sm">

        {{-- Gross subtotal (VAT-inclusive, before discount) --}}
        <div class="flex justify-between text-slate-600">
            <span>Subtotal (VAT incl.)</span>
            <span>&#8369;{{ number_format($sale->subtotal, 2) }}</span>
        </div>

        @if ($sale->discount_amount > 0)
            <div class="flex justify-between text-slate-600">
                <span>
                    Discount
                    @if ($sale->discount_percent > 0)
                        ({{ number_format($sale->discount_percent, 0) }}%)
                    @endif
                </span>
                <span class="text-red-600">&minus; &#8369;{{ number_format($sale->discount_amount, 2) }}</span>
            </div>
        @endif

        {{-- Total line (what the customer pays) --}}
        <div class="flex justify-between border-t border-slate-200 pt-2 text-lg font-black">
            <span>Total</span>
            <span>&#8369;{{ number_format($sale->total_amount, 2) }}</span>
        </div>

        {{-- VAT breakdown (back-calculated from the VAT-inclusive total) --}}
        <div class="mt-2 rounded-md bg-slate-50 px-3 py-2 text-xs text-slate-500 space-y-1">
            <div class="flex justify-between">
                <span>VAT-exclusive amount</span>
                <span>&#8369;{{ number_format($vatExcl, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span>VAT 12% (included)</span>
                <span>&#8369;{{ number_format($vatAmount, 2) }}</span>
            </div>
        </div>

        @if ($sale->payment_method === 'cash')
            <div class="flex justify-between text-slate-600 mt-2">
                <span>Cash Tendered</span>
                <span>&#8369;{{ number_format($sale->amount_tendered, 2) }}</span>
            </div>
            <div class="flex justify-between font-semibold text-teal-700">
                <span>Change</span>
                <span>&#8369;{{ number_format($sale->change_amount, 2) }}</span>
            </div>
        @endif

        <div class="flex justify-between text-slate-500 mt-1">
            <span>Payment</span>
            <span class="capitalize">{{ $sale->payment_method }}</span>
        </div>
    </div>

    {{-- QR Code --}}
    <div class="mt-6 flex flex-col items-center gap-2">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Scan to verify</p>
        <div class="rounded-lg border border-slate-200 p-2">
            {!! QrCode::size(120)->errorCorrection('M')->generate(
                route('cashier.receipt', $sale->id)
            ) !!}
        </div>
        <p class="text-xs text-slate-400">{{ $sale->reference }}</p>
    </div>

    {{-- Voided banner --}}
    @if ($sale->isVoided())
        <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-2 text-center text-sm font-bold text-red-700">
            &#9888; This sale has been VOIDED
            @if ($sale->void_reason)
                &mdash; {{ $sale->void_reason }}
            @endif
        </div>
    @endif

    {{-- Notes --}}
    @if ($sale->notes)
        <p class="mt-4 text-center text-xs text-slate-400">{{ $sale->notes }}</p>
    @endif

    {{-- Actions (hidden on print) --}}
    <div class="mt-6 grid grid-cols-2 gap-3 print:hidden">
        <button onclick="window.print()" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white">
            Print Receipt
        </button>
        <a href="{{ route('cashier.dashboard') }}" class="rounded-md border border-slate-300 px-4 py-2 text-center text-sm font-bold">
            New Sale
        </a>
    </div>
</section>