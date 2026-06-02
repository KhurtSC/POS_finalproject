<x-layout title="Add Product">
    @include('admin.products._form', [
        'product'    => null,
        'action'     => route('admin.products.store'),
        'method'     => 'POST',
        'categories' => $categories,
    ])
</x-layout>
