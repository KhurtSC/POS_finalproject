<x-layout title="Categories">
    <div class="mb-5 flex justify-end"><a href="{{ route('admin.categories.create') }}" class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white">Add Category</a></div>
    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left font-bold text-slate-500"><tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Description</th><th class="px-5 py-3">Product Count</th><th class="px-5 py-3">Actions</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse (($categories ?? []) as $category)
                    <tr><td class="px-5 py-4 font-bold">{{ $category->name }}</td><td class="px-5 py-4">{{ $category->description }}</td><td class="px-5 py-4">{{ $category->products_count }}</td><td class="px-5 py-4"><a class="font-bold text-teal-600" href="{{ route('admin.categories.edit', $category) }}">Edit</a></td></tr>
                @empty
                    @foreach (['Beverages', 'Food', 'Desserts'] as $name)
                        <tr><td class="px-5 py-4 font-bold">{{ $name }}</td><td class="px-5 py-4">Store category</td><td class="px-5 py-4">24</td><td class="px-5 py-4"><a class="font-bold text-teal-600" href="{{ route('admin.categories.edit', 1) }}">Edit</a></td></tr>
                    @endforeach
                @endforelse
            </tbody>
        </table>
    </section>
</x-layout>
