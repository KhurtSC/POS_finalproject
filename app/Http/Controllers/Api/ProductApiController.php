<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductApiController extends Controller
{
    // ── GET /api/products ────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $products = Product::with('category:id,name,slug')
            ->available()
            ->when($request->filled('category_id'), fn ($q) =>
                $q->inCategory((int) $request->category_id)
            )
            ->when($request->filled('search'), fn ($q) =>
                $q->where(function ($inner) use ($request) {
                    $term = '%' . $request->search . '%';
                    $inner->where('name', 'like', $term)
                          ->orWhere('sku', 'like', $term);
                })
            )
            ->orderBy('name')
            ->get(['id', 'category_id', 'name', 'sku', 'price', 'stock', 'image', 'is_available', 'low_stock_threshold']);

        return response()->json($products);
    }

    // ── POST /api/products ───────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id'         => ['required', 'exists:categories,id'],
            'name'                => ['required', 'string', 'max:255'],
            'sku'                 => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'description'         => ['nullable', 'string'],
            'price'               => ['required', 'numeric', 'min:0'],
            'cost'                => ['sometimes', 'numeric', 'min:0'],
            'stock'               => ['sometimes', 'integer', 'min:0'],
            'low_stock_threshold' => ['sometimes', 'integer', 'min:0'],
            'is_available'        => ['sometimes', 'boolean'],
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'event'       => 'product.created',
            'description' => 'Product "' . $product->name . '" created via API.',
            'context'     => ['price' => $product->price, 'stock' => $product->stock],
        ]);

        return response()->json($product, 201);
    }

    // ── PUT /api/products/{id} ───────────────────────────────────────────────

    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'category_id'         => ['sometimes', 'exists:categories,id'],
            'name'                => ['sometimes', 'string', 'max:255'],
            'sku'                 => ['nullable', 'string', 'max:100', 'unique:products,sku,' . $id],
            'description'         => ['nullable', 'string'],
            'price'               => ['sometimes', 'numeric', 'min:0'],
            'cost'                => ['sometimes', 'numeric', 'min:0'],
            'stock'               => ['sometimes', 'integer', 'min:0'],
            'low_stock_threshold' => ['sometimes', 'integer', 'min:0'],
            'is_available'        => ['sometimes', 'boolean'],
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $old = $product->only(array_keys($data));
        $product->update($data);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'event'       => 'product.updated',
            'description' => 'Product "' . $product->name . '" updated via API.',
            'context'     => ['old' => $old, 'new' => $data],
        ]);

        return response()->json($product);
    }

    // ── DELETE /api/products/{id} ────────────────────────────────────────────

    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $name    = $product->name;

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'event'       => 'product.deleted',
            'description' => 'Product "' . $name . '" deleted via API.',
        ]);

        return response()->json(['message' => "Product \"{$name}\" deleted."]);
    }
}
