<form method="POST" action="{{ $action }}"
      class="mx-auto max-w-3xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
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

    {{-- Name --}}
    <label class="block">
        <span class="text-sm font-bold">Name <span class="text-red-500">*</span></span>
        <input name="name" value="{{ old('name', $category->name ?? '') }}"
               class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2
                      @error('name') border-red-400 @enderror">
        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </label>

    {{-- Description --}}
    <label class="mt-5 block">
        <span class="text-sm font-bold">Description</span>
        <textarea name="description" rows="4"
                  class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2
                         @error('description') border-red-400 @enderror">{{ old('description', $category->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </label>

    {{-- Active toggle --}}
    <label class="mt-5 flex items-center gap-3">
        <input name="is_active" type="checkbox" value="1"
               class="h-5 w-5 rounded border-slate-300 text-teal-500"
               @checked(old('is_active', $category->is_active ?? true))>
        <span class="text-sm font-bold">Active (visible in product form)</span>
    </label>

    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('admin.categories.index') }}"
           class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold hover:bg-slate-50">
            Cancel
        </a>
        <button class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white hover:bg-teal-600">
            Save Category
        </button>
    </div>
</form>
