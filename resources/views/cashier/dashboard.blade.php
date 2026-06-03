<x-layout title="Cashier POS">
    <div class="grid gap-6 xl:grid-cols-[1fr_420px]" data-pos>
        <section>
            <div class="mb-4 grid gap-3 md:grid-cols-[1fr_220px]">
                <input data-product-search placeholder="Search products" class="rounded-md border border-slate-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                <select data-category-filter class="rounded-md border border-slate-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                    <option value="">All categories</option>
                    @foreach ($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Product grid --}}
            <div data-product-grid class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products ?? [] as $product)
                    <button type="button"
                        class="product-card overflow-hidden rounded-lg border border-slate-200 bg-white text-left shadow-sm transition hover:-translate-y-0.5 hover:border-teal-300 hover:shadow-md"
                        data-id="{{ $product->id }}"
                        data-name="{{ $product->name }}"
                        data-category="{{ $product->category_id }}"
                        data-price="{{ $product->price }}">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="h-40 w-full object-cover"
                                 loading="lazy">
                        @else
                            <div class="flex h-40 w-full items-center justify-center bg-slate-100 text-3xl">☕</div>
                        @endif
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="font-black text-slate-950">{{ $product->name }}</h2>
                                    <p class="text-sm font-semibold text-slate-500">{{ $product->category->name }}</p>
                                </div>
                                <p class="font-black text-teal-600">₱{{ number_format($product->price, 2) }}</p>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Stock {{ $product->stock }}</p>
                                @if ($product->isLowStock())
                                    <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-600">Low Stock</span>
                                @endif
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>

            {{-- Pagination controls --}}
            <div class="mt-5 flex items-center justify-between">
                <button data-prev-page
                    class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">
                    ← Prev
                </button>
                <span data-page-info class="text-sm font-semibold text-slate-500"></span>
                <button data-next-page
                    class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">
                    Next →
                </button>
            </div>
        </section>

        {{-- Cart sidebar --}}
        <aside class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-black text-slate-950">Cart</h2>
            </div>
            <div data-cart-items class="min-h-64 divide-y divide-slate-100"></div>
            <div class="space-y-3 border-t border-slate-200 p-5 text-sm">
                <div class="flex justify-between"><span class="font-semibold text-slate-500">Subtotal</span><span data-subtotal class="font-black">₱0.00</span></div>
                <div class="flex justify-between"><span class="font-semibold text-slate-500">VAT (12%)</span><span data-tax class="font-black">₱0.00</span></div>
                <div class="flex justify-between text-lg"><span class="font-black text-slate-950">Grand Total</span><span data-grand-total class="font-black text-teal-600">₱0.00</span></div>
                <div class="grid grid-cols-2 gap-3 pt-3">
                    <button type="button" data-clear-cart class="rounded-md border border-red-200 px-4 py-3 text-sm font-black text-red-600">Clear Cart</button>
                    <button type="button" data-checkout class="rounded-md bg-teal-500 px-4 py-3 text-sm font-black text-white">Checkout</button>
                </div>
            </div>
        </aside>
    </div>

    {{-- Checkout Modal --}}
    <div data-checkout-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">
        <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-black text-slate-950">Payment</h3>
            </div>

            <div class="space-y-4 px-6 py-5">

                {{-- Order summary --}}
                <div class="rounded-lg bg-slate-50 px-4 py-3 text-sm">
                    <div class="flex justify-between text-slate-500"><span>Subtotal</span><span data-modal-subtotal>₱0.00</span></div>
                    <div data-discount-row class="hidden flex justify-between text-red-600"><span>Discount</span><span data-modal-discount>−₱0.00</span></div>
                    <div class="mt-2 flex justify-between border-t border-slate-200 pt-2 text-base font-black text-slate-950"><span>Total</span><span data-modal-total>₱0.00</span></div>
                </div>

                {{-- Payment method --}}
                <div>
                    <label class="mb-1.5 block text-sm font-bold text-slate-700">Payment Method</label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach (['cash' => 'Cash', 'card' => 'Card', 'gcash' => 'GCash', 'other' => 'Other'] as $value => $label)
                            <label class="flex cursor-pointer flex-col items-center gap-1 rounded-lg border border-slate-200 p-3 text-center text-xs font-bold transition has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50 has-[:checked]:text-teal-700">
                                <input type="radio" name="payment_method" value="{{ $value }}" class="sr-only" {{ $value === 'cash' ? 'checked' : '' }}>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Discount --}}
                <div>
                    <label class="mb-1.5 block text-sm font-bold text-slate-700">Discount <span class="font-normal text-slate-400">(optional)</span></label>
                    <div class="flex gap-2">
                        <input type="number" data-discount-input min="0" placeholder="0" class="w-full rounded-md border border-slate-300 px-4 py-2 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                        <select data-discount-type class="rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500">
                            <option value="percent">%</option>
                            <option value="flat">₱</option>
                        </select>
                    </div>
                </div>

                {{-- Cash tendered (shown only for cash) --}}
                <div data-cash-section>
                    <label class="mb-1.5 block text-sm font-bold text-slate-700">Cash Tendered</label>
                    <input type="number" data-tendered-input min="0" placeholder="0.00" class="w-full rounded-md border border-slate-300 px-4 py-2 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                    <div class="mt-2 flex justify-between rounded-lg bg-teal-50 px-4 py-2 text-sm font-black text-teal-700">
                        <span>Change</span>
                        <span data-change-display>₱0.00</span>
                    </div>
                </div>

            </div>

            <div class="flex gap-3 border-t border-slate-200 px-6 py-4">
                <button type="button" data-modal-cancel class="flex-1 rounded-md border border-slate-300 px-4 py-2.5 text-sm font-black text-slate-700">Cancel</button>
                <button type="button" data-modal-confirm class="flex-1 rounded-md bg-teal-500 px-4 py-2.5 text-sm font-black text-white">Confirm Sale</button>
            </div>
        </div>
    </div>

    @push('scripts')<script src="{{ asset('assets/js/pos.js') }}"></script>@endpush
</x-layout>