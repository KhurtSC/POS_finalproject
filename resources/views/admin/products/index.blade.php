<x-layout title="Products">
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <form class="grid gap-3 sm:grid-cols-[1fr_220px] md:w-2/3">
            <input name="search" placeholder="Search products" class="rounded-md border border-slate-300 px-4 py-2 text-sm">
            <select name="category" class="rounded-md border border-slate-300 px-4 py-2 text-sm"><option>All categories</option></select>
        </form>
        <div class="flex gap-2">
            <button class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold">Import CSV</button>
            <a href="{{ route('admin.products.create') }}" class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white">Add Product</a>
        </div>
    </div>
    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left font-bold text-slate-500"><tr><th class="px-5 py-3">Product Name</th><th class="px-5 py-3">Category</th><th class="px-5 py-3">Price</th><th class="px-5 py-3">Stock</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse (($products ?? []) as $product)
                        <tr>
                            <td class="px-5 py-4 font-bold">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-md bg-slate-100"></div>
                                    <span>{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">{{ $product->category->name }}</td>
                            <td class="px-5 py-4">₱{{ number_format($product->price, 2) }}</td>
                            <td class="px-5 py-4">{{ $product->stock }}</td>
                            <td class="px-5 py-4"><span class="rounded-full bg-teal-50 px-2 py-1 text-xs font-bold text-teal-700">{{ $product->status }}</span></td>
                            <td class="px-5 py-4"><a class="font-bold text-teal-600" href="{{ route('admin.products.edit', $product) }}">Edit</a> <button data-confirm="Delete this product?" class="ml-3 font-bold text-red-600">Delete</button></td>
                        </tr>
                    @empty
                        @php
                            $demoRows = [
                                ['Iced Coffee', 'Beverages', 150, 'iced-coffee.jpg'],
                                ['White Mocha', 'Beverages', 175, 'white-mocha.jpg'],
                                ['Club Sandwich', 'Food', 220, 'club-sandwich.jpg'],
                                ['Truffle Pasta', 'Food', 260, 'truffle-pasta.jpg'],
                                ['Chocolate Mousse', 'Desserts', 145, 'chocolate-mousse.jpg'],
                                ['Cafe Dessert Cup', 'Desserts', 155, 'cafe-dessert.jpg'],
                            ];
                        @endphp
                        @foreach ($demoRows as [$name, $category, $price, $image])
                            <tr>
                                <td class="px-5 py-4 font-bold">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset('assets/images/products/'.$image) }}" alt="{{ $name }}" class="h-12 w-12 rounded-md object-cover">
                                        <span>{{ $name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">{{ $category }}</td>
                                <td class="px-5 py-4">₱{{ number_format($price, 2) }}</td>
                                <td class="px-5 py-4">48</td>
                                <td class="px-5 py-4"><span class="rounded-full bg-teal-50 px-2 py-1 text-xs font-bold text-teal-700">Active</span></td>
                                <td class="px-5 py-4"><a class="font-bold text-teal-600" href="{{ route('admin.products.edit', 1) }}">Edit</a> <button data-confirm="Delete this product?" class="ml-3 font-bold text-red-600">Delete</button></td>
                            </tr>
                        @endforeach
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between border-t border-slate-200 px-5 py-4 text-sm font-semibold text-slate-500"><span>Showing 1-10</span><span>{{ isset($products) ? $products->links() : 'Pagination' }}</span></div>
    </section>
</x-layout>
