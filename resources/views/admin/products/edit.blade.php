<x-layout title="Edit Product">
    @include('admin.products._form', [
        'product'    => $product,
        'action'     => route('admin.products.update', $product),
        'method'     => 'PUT',
        'categories' => $categories,
    ])
</x-layout>
