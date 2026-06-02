<section class="mx-auto max-w-md rounded-lg border border-slate-200 bg-white p-6 shadow-sm print:border-0 print:shadow-none">
    <div class="text-center">
        <img src="{{ asset('assets/images/brand/pointsale-logo.svg') }}" alt="Cafe POS" class="mx-auto mb-3 h-14 w-auto rounded-lg print:h-10">
        <h1 class="text-xl font-black text-slate-950">Cafe POS Receipt</h1>
        <p class="text-sm font-medium text-slate-500">Sale #{{ $sale->id ?? '2042' }} - {{ optional($sale->created_at ?? now())->format('M j, Y g:i A') }}</p>
        <p class="text-sm font-medium text-slate-500">Cashier: {{ $sale->cashier->name ?? 'Demo Cashier' }}</p>
    </div>
    <div class="my-6 divide-y divide-slate-100 border-y border-slate-200">
        @forelse (($sale->items ?? []) as $item)
            <div class="flex justify-between py-3 text-sm"><span>{{ $item->product->name }} x {{ $item->quantity }}</span><span>₱{{ number_format($item->subtotal, 2) }}</span></div>
        @empty
            <div class="flex justify-between py-3 text-sm"><span>Iced Coffee x 2</span><span>₱300.00</span></div>
            <div class="flex justify-between py-3 text-sm"><span>Club Sandwich x 1</span><span>₱220.00</span></div>
            <div class="flex justify-between py-3 text-sm"><span>Chocolate Mousse x 1</span><span>₱145.00</span></div>
        @endforelse
    </div>
    <div class="flex justify-between text-lg font-black"><span>Total</span><span>₱{{ number_format($sale->total ?? 665, 2) }}</span></div>
    <div class="mt-6 grid grid-cols-2 gap-3 print:hidden">
        <button onclick="window.print()" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white">Print Receipt</button>
        <a href="{{ route('cashier.dashboard') }}" class="rounded-md border border-slate-300 px-4 py-2 text-center text-sm font-bold">New Sale</a>
    </div>
</section>
