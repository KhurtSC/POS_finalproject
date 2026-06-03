<x-layout title="Product QR Label">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 print:hidden">
        <a href="{{ route('admin.products.index') }}"
           class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
            Back to Products
        </a>
        <button type="button" onclick="window.print()"
            class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white hover:bg-teal-600">
            Print Label
        </button>
    </div>

    <section class="mx-auto max-w-sm rounded-lg border border-slate-200 bg-white p-6 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <p class="text-xs font-black uppercase tracking-wide text-slate-400">PointSale Product Label</p>
        <h2 class="mt-2 text-2xl font-black text-slate-950 dark:text-white">{{ $product->name }}</h2>
        <p class="mt-1 text-sm font-semibold text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</p>

        <div class="qr-code-surface mx-auto mt-5 inline-block rounded-lg border border-slate-200 bg-white p-4">
            {!! QrCode::size(190)->errorCorrection('M')->generate($scanPayload) !!}
        </div>

        <div class="mt-5 rounded-lg bg-slate-50 p-4 dark:bg-slate-800">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">SKU / Barcode Value</p>
            <p class="mt-1 font-mono text-xl font-black text-slate-950 dark:text-white">{{ $code }}</p>
            <div class="mx-auto mt-3 h-12 max-w-64 overflow-hidden rounded bg-white px-2 py-1">
                <div class="h-full w-full barcode-stripes"></div>
            </div>
        </div>

        <p class="mt-5 text-sm font-bold text-teal-600">PHP {{ number_format($product->price, 2) }}</p>
        <p class="mt-1 text-xs font-semibold text-slate-400">Scan this QR code or enter the SKU in the cashier POS.</p>
    </section>
</x-layout>
