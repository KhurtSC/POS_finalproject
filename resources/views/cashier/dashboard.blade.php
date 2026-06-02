<x-layout title="Cashier POS">
    <div class="grid gap-6 xl:grid-cols-[1fr_420px]" data-pos>
        <section>
            <div class="mb-4 grid gap-3 md:grid-cols-[1fr_220px]">
                <input data-product-search placeholder="Search products" class="rounded-md border border-slate-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                <select data-category-filter class="rounded-md border border-slate-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                    <option value="">All categories</option>
                    <option>Beverages</option>
                    <option>Food</option>
                    <option>Desserts</option>
                </select>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $demoProducts = $products ?? collect([
                        (object)['name' => 'Iced Coffee', 'category' => 'Beverages', 'price' => 150, 'stock' => 38, 'image' => 'iced-coffee.jpg'],
                        (object)['name' => 'White Mocha', 'category' => 'Beverages', 'price' => 175, 'stock' => 52, 'image' => 'white-mocha.jpg'],
                        (object)['name' => 'Club Sandwich', 'category' => 'Food', 'price' => 220, 'stock' => 24, 'image' => 'club-sandwich.jpg'],
                        (object)['name' => 'Truffle Pasta', 'category' => 'Food', 'price' => 260, 'stock' => 18, 'image' => 'truffle-pasta.jpg'],
                        (object)['name' => 'Chocolate Mousse', 'category' => 'Desserts', 'price' => 145, 'stock' => 30, 'image' => 'chocolate-mousse.jpg'],
                        (object)['name' => 'Cafe Dessert Cup', 'category' => 'Desserts', 'price' => 155, 'stock' => 20, 'image' => 'cafe-dessert.jpg'],
                    ]);
                @endphp

                @foreach ($demoProducts as $product)
                    <button type="button" class="product-card overflow-hidden rounded-lg border border-slate-200 bg-white text-left shadow-sm transition hover:-translate-y-0.5 hover:border-teal-300 hover:shadow-md" data-name="{{ $product->name }}" data-category="{{ $product->category }}" data-price="{{ $product->price }}">
                        <img src="{{ asset('assets/images/products/'.$product->image) }}" alt="{{ $product->name }}" class="h-40 w-full object-cover">
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="font-black text-slate-950">{{ $product->name }}</h2>
                                    <p class="text-sm font-semibold text-slate-500">{{ $product->category }}</p>
                                </div>
                                <p class="font-black text-teal-600">₱{{ number_format($product->price, 2) }}</p>
                            </div>
                            <p class="mt-3 text-xs font-bold uppercase tracking-wide text-slate-400">Stock {{ $product->stock }}</p>
                        </div>
                    </button>
                @endforeach
            </div>
        </section>

        <aside class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-black text-slate-950">Cart</h2>
            </div>
            <div data-cart-items class="min-h-64 divide-y divide-slate-100"></div>
            <div class="space-y-3 border-t border-slate-200 p-5 text-sm">
                <div class="flex justify-between"><span class="font-semibold text-slate-500">Subtotal</span><span data-subtotal class="font-black">₱0.00</span></div>
                <div class="flex justify-between"><span class="font-semibold text-slate-500">VAT</span><span data-tax class="font-black">₱0.00</span></div>
                <div class="flex justify-between text-lg"><span class="font-black text-slate-950">Grand Total</span><span data-grand-total class="font-black text-teal-600">₱0.00</span></div>
                <div class="grid grid-cols-2 gap-3 pt-3">
                    <button type="button" data-clear-cart class="rounded-md border border-red-200 px-4 py-3 text-sm font-black text-red-600">Clear Cart</button>
                    <button type="button" data-checkout class="rounded-md bg-teal-500 px-4 py-3 text-sm font-black text-white">Checkout</button>
                </div>
            </div>
        </aside>
    </div>
    @push('scripts')<script src="{{ asset('assets/js/pos.js') }}"></script>@endpush
</x-layout>
