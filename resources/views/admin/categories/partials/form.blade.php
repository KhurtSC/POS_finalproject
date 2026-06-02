<form method="POST" action="{{ $action }}" class="mx-auto max-w-3xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif
    <label class="block"><span class="text-sm font-bold">Name</span><input name="name" value="{{ old('name', $category->name ?? '') }}" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2"></label>
    <label class="mt-5 block"><span class="text-sm font-bold">Description</span><textarea name="description" rows="4" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2">{{ old('description', $category->description ?? '') }}</textarea></label>
    <div class="mt-6 flex justify-end gap-3"><a href="{{ route('admin.categories.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold">Cancel</a><button class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white">Save Category</button></div>
</form>
