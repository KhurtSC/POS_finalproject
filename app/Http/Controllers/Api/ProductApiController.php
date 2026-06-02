<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
    // ── GET /api/products ────────────────────────────────────────────────────
    // Used by the POS screen to populate the product grid.

    public function index(Request $request): JsonResponse
    {
        $products = Product::with('category:id,name,slug')
            ->available()                          // is_available = true AND stock > 0
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
        ]);

        $product = Product::create($data);

        ActivityLogger::log(
            event: 'product.created',
            description: "Product \"{$product->name}\" created by " . Auth::user()->name . ".",
            subject: $product,
            context: ['price' => $product->price, 'stock' => $product->stock],
        );

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
        ]);

        $old = $product->only(array_keys($data));
        $product->update($data);

        ActivityLogger::log(
            event: 'product.updated',
            description: "Product \"{$product->name}\" updated by " . Auth::user()->name . ".",
            subject: $product,
            context: ['old' => $old, 'new' => $data],
        );

        return response()->json($product);
    }

    // ── DELETE /api/products/{id} ────────────────────────────────────────────

    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $name    = $product->name;

        $product->delete(); // soft delete

        ActivityLogger::log(
            event: 'product.deleted',
            description: "Product \"{$name}\" deleted by " . Auth::user()->name . ".",
            subject: $product,
        );

        return response()->json(['message' => "Product \"{$name}\" deleted."]);
    }
}