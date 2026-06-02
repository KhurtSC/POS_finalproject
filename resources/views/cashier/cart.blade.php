<x-layout title="Cart">
    <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-black text-slate-950">Current Checkout</h2>
        <p class="mt-2 text-sm font-medium text-slate-500">Use the POS screen to add products, adjust quantities, and process a sale.</p>
        <a href="{{ route('cashier.dashboard') }}" class="mt-6 inline-flex rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white">Open POS</a>
    </section>
</x-layout>
