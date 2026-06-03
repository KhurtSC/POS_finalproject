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

    public function label(Product $product): View
    {
        $code = $product->sku ?: (string) $product->id;
        $scanPayload = url('/cashier/dashboard') . '?sku=' . urlencode($code);

        return view('admin.products.label', compact('product', 'code', 'scanPayload'));
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

    // ── P2.4 — CSV Import ─────────────────────────────────────────────────────

    public function importForm(): View
    {
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);

        return view('admin.products.import', compact('categories'));
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Read and validate header row
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'The CSV file is empty.']);
        }

        // Normalize header names (lowercase, trim whitespace)
        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $required = ['name', 'price', 'stock', 'category_id'];
        $missing  = array_diff($required, $header);

        if (!empty($missing)) {
            fclose($handle);
            return back()->withErrors([
                'csv_file' => 'Missing required columns: ' . implode(', ', $missing) . '.',
            ]);
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $rowNum   = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            // Skip completely blank rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map row values to column names
            $data = array_combine($header, array_pad($row, count($header), null));

            // Basic per-row validation
            if (empty(trim($data['name'] ?? ''))) {
                $errors[] = "Row {$rowNum}: 'name' is required.";
                $skipped++;
                continue;
            }

            if (!is_numeric($data['price'] ?? '') || (float) $data['price'] < 0) {
                $errors[] = "Row {$rowNum}: 'price' must be a non-negative number (got \"{$data['price']}\").";
                $skipped++;
                continue;
            }

            if (!ctype_digit((string) ($data['stock'] ?? '')) || (int) $data['stock'] < 0) {
                $errors[] = "Row {$rowNum}: 'stock' must be a non-negative integer (got \"{$data['stock']}\").";
                $skipped++;
                continue;
            }

            $categoryId = (int) ($data['category_id'] ?? 0);
            if (!Category::where('id', $categoryId)->exists()) {
                $errors[] = "Row {$rowNum}: category_id {$categoryId} does not exist.";
                $skipped++;
                continue;
            }

            // Check for duplicate SKU
            $sku = !empty(trim($data['sku'] ?? '')) ? trim($data['sku']) : null;
            if ($sku && Product::where('sku', $sku)->exists()) {
                $errors[] = "Row {$rowNum}: SKU \"{$sku}\" already exists — skipped.";
                $skipped++;
                continue;
            }

            Product::create([
                'category_id'         => $categoryId,
                'name'                => trim($data['name']),
                'sku'                 => $sku,
                'description'         => trim($data['description'] ?? '') ?: null,
                'price'               => (float) $data['price'],
                'cost'                => isset($data['cost']) && is_numeric($data['cost']) ? (float) $data['cost'] : 0,
                'stock'               => (int) $data['stock'],
                'low_stock_threshold' => 5,
                'is_available'        => isset($data['is_available']) ? (bool)(int)$data['is_available'] : true,
            ]);

            $imported++;
        }

        fclose($handle);

        $message = "Import complete: {$imported} product(s) imported.";
        if ($skipped > 0) {
            $message .= " {$skipped} row(s) skipped.";
        }

        $session = ['success' => $message];
        if (!empty($errors)) {
            $session['import_errors'] = $errors;
        }

        return redirect()->route('admin.products.index')->with($session);
    }
}
