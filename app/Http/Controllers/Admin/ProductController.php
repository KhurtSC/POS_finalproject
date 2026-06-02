<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);

        $products = Product::with('category:id,name')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->search . '%';
                $q->where(fn ($inner) =>
                    $inner->where('name', 'like', $term)
                          ->orWhere('sku', 'like', $term)
                );
            })
            ->when($request->filled('category_id'), fn ($q) =>
                $q->where('category_id', $request->category_id)
            )
            ->when($request->filled('stock'), function ($q) use ($request) {
                match ($request->stock) {
                    'low'  => $q->lowStock(),
                    'out'  => $q->where('stock', 0),
                    default => null,
                };
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);

        return view('admin.products.create', [
            'action'     => route('admin.products.store'),
            'method'     => 'POST',
            'product'    => null,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id'         => ['required', 'exists:categories,id'],
            'name'                => ['required', 'string', 'max:255'],
            'sku'                 => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'description'         => ['nullable', 'string'],
            'price'               => ['required', 'numeric', 'min:0'],
            'cost'                => ['nullable', 'numeric', 'min:0'],
            'stock'               => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'is_available'        => ['sometimes', 'boolean'],
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_available']        = $request->boolean('is_available', true);
        $data['cost']                = $data['cost'] ?? 0;
        $data['low_stock_threshold'] = $data['low_stock_threshold'] ?? 5;

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);

        return view('admin.products.edit', [
            'action'     => route('admin.products.update', $product),
            'method'     => 'PUT',
            'product'    => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'category_id'         => ['required', 'exists:categories,id'],
            'name'                => ['required', 'string', 'max:255'],
            'sku'                 => ['nullable', 'string', 'max:100', 'unique:products,sku,' . $product->id],
            'description'         => ['nullable', 'string'],
            'price'               => ['required', 'numeric', 'min:0'],
            'cost'                => ['nullable', 'numeric', 'min:0'],
            'stock'               => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'is_available'        => ['sometimes', 'boolean'],
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_available'] = $request->boolean('is_available', true);

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete(); // soft delete

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}