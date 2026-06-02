<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleApiController extends Controller
{
    // ── GET /api/sales ───────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $query = Sale::with(['cashier:id,name', 'items'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('cashier_id')) {
            $query->forCashier((int) $request->cashier_id);
        }

        if ($request->filled('date')) {
            $query->forDate($request->date);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->forDateRange($request->from, $request->to);
        }

        return response()->json($query->paginate(20));
    }

    // ── POST /api/sales ──────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1'],
            'discount_percent'       => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'discount_amount'        => ['sometimes', 'numeric', 'min:0'],
            'payment_method'         => ['sometimes', 'in:cash,card,gcash,other'],
            'amount_tendered'        => ['sometimes', 'numeric', 'min:0'],
            'notes'                  => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        $sale = DB::transaction(function () use ($data) {
            // ── 1. Load & lock products ──────────────────────────────────────
            $productIds = collect($data['items'])->pluck('product_id')->unique();

            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // ── 2. Validate stock and availability ───────────────────────────
            foreach ($data['items'] as $line) {
                $product = $products->get($line['product_id']);

                if (! $product || ! $product->is_available) {
                    abort(422, "Product #{$line['product_id']} is not available.");
                }

                if ($product->stock < $line['quantity']) {
                    abort(422, "Insufficient stock for \"{$product->name}\" (requested {$line['quantity']}, available {$product->stock}).");
                }
            }

            // ── 3. Compute totals ────────────────────────────────────────────
            $subtotal = 0;
            $saleItems = [];

            foreach ($data['items'] as $line) {
                $product   = $products->get($line['product_id']);
                $lineTotal = $product->price * $line['quantity'];
                $subtotal += $lineTotal;

                $saleItems[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'product_sku'  => $product->sku,
                    'unit_price'   => $product->price,
                    'quantity'     => $line['quantity'],
                    'subtotal'     => $lineTotal,
                ];
            }

            // Discount — percent takes precedence if both are sent
            $discountPercent = (float) ($data['discount_percent'] ?? 0);
            $discountAmount  = $discountPercent > 0
                ? round($subtotal * $discountPercent / 100, 2)
                : (float) ($data['discount_amount'] ?? 0);

            $total          = max(0, $subtotal - $discountAmount);
            $amountTendered = (float) ($data['amount_tendered'] ?? 0);
            $changeAmount   = max(0, $amountTendered - $total);

            // ── 4. Create Sale ───────────────────────────────────────────────
            $sale = Sale::create([
                'user_id'          => Auth::id(),
                'subtotal'         => $subtotal,
                'discount_percent' => $discountPercent,
                'discount_amount'  => $discountAmount,
                'total_amount'     => $total,
                'payment_method'   => $data['payment_method'] ?? 'cash',
                'amount_tendered'  => $amountTendered,
                'change_amount'    => $changeAmount,
                'status'           => 'completed',
                'notes'            => $data['notes'] ?? null,
            ]);

            // ── 5. Create SaleItems ──────────────────────────────────────────
            $sale->items()->createMany($saleItems);

            // ── 6. Deduct stock ──────────────────────────────────────────────
            foreach ($data['items'] as $line) {
                $products->get($line['product_id'])->decrementStock($line['quantity']);
            }

            return $sale;
        });

        ActivityLogger::log(
            event: 'sale.created',
            description: "Sale #{$sale->reference} processed by " . Auth::user()->name . ".",
            subject: $sale,
            context: [
                'total'          => $sale->total_amount,
                'payment_method' => $sale->payment_method,
                'item_count'     => count($data['items']),
            ],
        );

        return response()->json([
            'message' => 'Sale created successfully.',
            'sale_id' => $sale->id,
            'reference' => $sale->reference,
            'total_amount' => $sale->total_amount,
            'change_amount' => $sale->change_amount,
        ], 201);
    }

    // ── GET /api/sales/{id} ──────────────────────────────────────────────────

    public function show(int $id): JsonResponse
    {
        $sale = Sale::with([
            'items',
            'cashier:id,name,email',
            'voidedBy:id,name',
        ])->findOrFail($id);

        return response()->json($sale);
    }

    // ── POST /api/sales/{id}/void ────────────────────────────────────────────

    public function void(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $sale = Sale::with('items')->findOrFail($id);

        if ($sale->isVoided()) {
            return response()->json(['message' => 'Sale is already voided.'], 422);
        }

        DB::transaction(function () use ($sale, $data) {
            // ── Restock items ────────────────────────────────────────────────
            foreach ($sale->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)
                        ->lockForUpdate()
                        ->first()
                        ?->incrementStock($item->quantity);
                }
            }

            // ── Mark voided ──────────────────────────────────────────────────
            $sale->update([
                'status'      => 'voided',
                'void_reason' => $data['reason'],
                'voided_by'   => Auth::id(),
                'voided_at'   => now(),
            ]);
        });

        ActivityLogger::log(
            event: 'sale.voided',
            description: "Sale #{$sale->reference} voided by " . Auth::user()->name . ".",
            subject: $sale,
            context: [
                'reason'    => $data['reason'],
                'voided_by' => Auth::user()->name,
            ],
        );

        return response()->json(['message' => 'Sale voided successfully.']);
    }
}