<x-layout title="Import Products from CSV">
    <div class="mx-auto max-w-2xl">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-950">Import Products</h1>
                <p class="text-sm text-slate-500 mt-1">Upload a CSV file to bulk-create products.</p>
            </div>
            <a href="{{ route('admin.products.index') }}"
               class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold hover:bg-slate-50">
                ← Back to Products
            </a>
        </div>

        <x-alert />

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">

            <h2 class="font-black text-slate-950 mb-1">CSV Format</h2>
            <p class="text-sm text-slate-500 mb-4">
                The first row must be a header. Required columns: <code class="font-mono bg-slate-100 px-1 rounded">name</code>,
                <code class="font-mono bg-slate-100 px-1 rounded">price</code>,
                <code class="font-mono bg-slate-100 px-1 rounded">stock</code>,
                <code class="font-mono bg-slate-100 px-1 rounded">category_id</code>.
                Optional: <code class="font-mono bg-slate-100 px-1 rounded">sku</code>,
                <code class="font-mono bg-slate-100 px-1 rounded">description</code>,
                <code class="font-mono bg-slate-100 px-1 rounded">is_available</code> (1 or 0).
            </p>

            <div class="mb-5 rounded-md bg-slate-50 border border-slate-200 p-3 text-xs font-mono text-slate-600 overflow-x-auto">
                name,price,stock,category_id,sku,description,is_available<br>
                Espresso,90,100,1,ESP-001,Strong black coffee,1<br>
                Croissant,85,50,2,CRS-001,Buttery pastry,1
            </div>

            <form method="POST" action="{{ route('admin.products.import.csv') }}"
                  enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Choose CSV File
                    </label>
                    <input type="file" name="csv_file" accept=".csv,text/csv" required
                           class="block w-full text-sm text-slate-500
                                  file:mr-4 file:rounded-md file:border-0
                                  file:bg-teal-500 file:px-4 file:py-2
                                  file:text-sm file:font-bold file:text-white
                                  hover:file:bg-teal-600 cursor-pointer">
                    @error('csv_file')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="rounded-md bg-slate-950 px-6 py-2 text-sm font-bold text-white hover:bg-slate-800">
                    Import Products
                </button>
            </form>
        </section>

        {{-- Available categories for reference --}}
        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-black text-slate-950 mb-3">Available Categories</h2>
            <table class="min-w-full text-sm divide-y divide-slate-100">
                <thead class="text-left font-bold text-slate-500">
                    <tr>
                        <th class="py-2 pr-4">ID</th>
                        <th class="py-2">Name</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($categories as $cat)
                        <tr>
                            <td class="py-2 pr-4 font-mono text-slate-400">{{ $cat->id }}</td>
                            <td class="py-2 font-medium">{{ $cat->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

    </div>
</x-layout>