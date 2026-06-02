<x-layout title="Products">
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <form method="GET" action="{{ route('admin.products.index') }}"
              class="grid gap-3 sm:grid-cols-[1fr_220px] md:w-2/3">
            <input name="search" value="{{ request('search') }}"
                   placeholder="Search products"
                   class="rounded-md border border-slate-300 px-4 py-2 text-sm">
            <select name="category" class="rounded-md border border-slate-300 px-4 py-2 text-sm">
                <option value="">All categories</option>
                @foreach ($categories ?? [] as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </form>
        <div class="flex gap-2">
            {{-- P2.4: Import CSV button now links to the import form --}}
            <a href="{{ route('admin.products.import.form') }}"
               class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold hover:bg-slate-50">
                Import CSV
            </a>
            <a href="{{ route('admin.products.create') }}"
               class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white hover:bg-teal-600">
                Add Product
            </a>
        </div>
    </div>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left font-bold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Product Name</th>
                        <th class="px-5 py-3">Category</th>
                        <th class="px-5 py-3">Price</th>
                        <th class="px-5 py-3">Stock</th>
                        {{-- P1.3: "Status" column now uses is_available, not the non-existent ->status --}}
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse (($products ?? []) as $product)
                        <tr>
                            <td class="px-5 py-4 font-bold">
                                <div class="flex items-center gap-3">
                                    @if ($product->image)
                                        <img src="{{ Storage::url($product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="h-12 w-12 rounded-md object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-md bg-slate-100 flex items-center justify-center text-slate-400 text-xs">
                                            No img
                                        </div>
                                    @endif
                                    <div>
                                        <span>{{ $product->name }}</span>
                                        @if ($product->sku)
                                            <p class="text-xs text-slate-400">{{ $product->sku }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">{{ $product->category->name ?? '—' }}</td>
                            <td class="px-5 py-4">₱{{ number_format($product->price, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="{{ $product->stock <= ($product->low_stock_threshold ?? 5) && $product->stock > 0
                                    ? 'text-amber-600 font-bold'
                                    : ($product->stock == 0 ? 'text-red-600 font-bold' : '') }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            {{-- P1.3 fix: derive status from is_available and stock --}}
                            <td class="px-5 py-4">
                                @if (!$product->is_available || $product->stock == 0)
                                    <span class="rounded-full bg-red-50 px-2 py-1 text-xs font-bold text-red-700">
                                        Inactive
                                    </span>
                                @else
                                    <span class="rounded-full bg-teal-50 px-2 py-1 text-xs font-bold text-teal-700">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <a class="font-bold text-teal-600 hover:underline"
                                   href="{{ route('admin.products.edit', $product) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                      class="inline" onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ml-3 font-bold text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                No products found.
                                <a href="{{ route('admin.products.create') }}" class="text-teal-600 font-bold hover:underline">Add the first one</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between border-t border-slate-200 px-5 py-4 text-sm font-semibold text-slate-500">
            <span>{{ isset($products) ? 'Showing ' . $products->firstItem() . '–' . $products->lastItem() . ' of ' . $products->total() : '' }}</span>
            <span>{{ isset($products) ? $products->links() : '' }}</span>
        </div>
    </section>
</x-layout>