<x-layout title="Add Product">
    @include('admin.products.partials.form', ['product' => null, 'action' => route('admin.products.store'), 'method' => 'POST'])
</x-layout>
