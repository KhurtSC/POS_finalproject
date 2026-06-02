<x-layout title="Edit Product">
    @include('admin.products.partials.form', ['product' => $product ?? null, 'action' => route('admin.products.update', $product ?? 1), 'method' => 'PUT'])
</x-layout>
