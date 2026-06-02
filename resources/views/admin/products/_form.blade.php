<form method="POST" action="{{ $action }}" enctype="multipart/form-data"
      class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif

    @if ($errors->any())
        <div class="mb-5 rounded-md bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-2">

        {{-- Name --}}
        <label class="block">
            <span class="text-sm font-bold">Name <span class="text-red-500">*</span></span>
            <input name="name" value="{{ old('name', $product->name ?? '') }}"
                   class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2
                          @error('name') border-red-400 @enderror">
            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </label>

        {{-- Category --}}
        <label class="block">
            <span class="text-sm font-bold">Category <span class="text-red-500">*</span></span>
            <select name="category_id"
                    class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2
                           @error('category_id') border-red-400 @enderror">
                <option value="">Select category</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}"
                        @selected(old('category_id', $product->category_id ?? '') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </label>

        {{-- SKU --}}
        <label class="block">
            <span class="text-sm font-bold">SKU / Barcode</span>
            <input name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                   class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2
                          @error('sku') border-red-400 @enderror">
            @error('sku') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </label>

        {{-- Price --}}
        <label class="block">
            <span class="text-sm font-bold">Selling Price (₱) <span class="text-red-500">*</span></span>
            <input name="price" type="number" step="0.01" min="0"
                   value="{{ old('price', $product->price ?? '') }}"
                   class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2
                          @error('price') border-red-400 @enderror">
            @error('price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </label>

        {{-- Cost --}}
        <label class="block">
            <span class="text-sm font-bold">Cost Price (₱)</span>
            <input name="cost" type="number" step="0.01" min="0"
                   value="{{ old('cost', $product->cost ?? '') }}"
                   class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2">
        </label>

        {{-- Stock --}}
        <label class="block">
            <span class="text-sm font-bold">Stock Quantity <span class="text-red-500">*</span></span>
            <input name="stock" type="number" min="0"
                   value="{{ old('stock', $product->stock ?? 0) }}"
                   class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2
                          @error('stock') border-red-400 @enderror">
            @error('stock') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </label>

        {{-- Low-stock threshold --}}
        <label class="block">
            <span class="text-sm font-bold">Low-Stock Alert Threshold</span>
            <input name="low_stock_threshold" type="number" min="0"
                   value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}"
                   class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2">
        </label>

        {{-- Description --}}
        <label class="block lg:col-span-2">
            <span class="text-sm font-bold">Description</span>
            <textarea name="description" rows="4"
                      class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2">{{ old('description', $product->description ?? '') }}</textarea>
        </label>

        {{-- Image --}}
        <label class="block">
            <span class="text-sm font-bold">Product Image</span>
            <input name="image" type="file" accept="image/jpeg,image/png,image/webp"
                   class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2">
            <p class="mt-1 text-xs text-slate-400">JPG, PNG, or WebP. Max 5 MB.</p>
            @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            @if (!empty($product->image))
                <div class="mt-3">
                    <img src="{{ Storage::disk('public')->url($product->image) }}"
                         alt="Current image" class="h-24 w-24 rounded-md object-cover border border-slate-200">
                    <p class="mt-1 text-xs text-slate-400">Current image — upload a new one to replace it.</p>
                </div>
            @endif
        </label>

        {{-- Available toggle --}}
        <label class="flex items-center gap-3 pt-8">
            <input name="is_available" type="checkbox" value="1"
                   class="h-5 w-5 rounded border-slate-300 text-teal-500"
                   @checked(old('is_available', $product->is_available ?? true))>
            <span class="text-sm font-bold">Available for sale</span>
        </label>
    </div>

    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('admin.products.index') }}"
           class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold hover:bg-slate-50">
            Cancel
        </a>
        <button class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white hover:bg-teal-600">
            Save Product
        </button>
    </div>
</form>