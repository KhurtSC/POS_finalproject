<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif
    <div class="grid gap-5 lg:grid-cols-2">
        <label class="block"><span class="text-sm font-bold">Name</span><input name="name" value="{{ old('name', $product->name ?? '') }}" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2"></label>
        <label class="block"><span class="text-sm font-bold">Category</span><select name="category_id" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2"><option>Select category</option></select></label>
        <label class="block"><span class="text-sm font-bold">Price</span><input name="price" type="number" step="0.01" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2"></label>
        <label class="block"><span class="text-sm font-bold">Stock</span><input name="stock" type="number" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2"></label>
        <label class="block lg:col-span-2"><span class="text-sm font-bold">Description</span><textarea name="description" rows="4" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2">{{ old('description', $product->description ?? '') }}</textarea></label>
        <label class="block"><span class="text-sm font-bold">Image</span><input name="image" type="file" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2"></label>
        <label class="flex items-center gap-3 pt-8"><input name="is_active" type="checkbox" class="h-5 w-5 rounded border-slate-300 text-teal-500" checked><span class="text-sm font-bold">Active</span></label>
    </div>
    <div class="mt-6 flex justify-end gap-3"><a href="{{ route('admin.products.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold">Cancel</a><button class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white">Save Product</button></div>
</form>
