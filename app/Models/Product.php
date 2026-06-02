<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price'               => 'decimal:2',
            'cost'                => 'decimal:2',
            'stock'               => 'integer',
            'low_stock_threshold' => 'integer',
            'is_available'        => 'boolean',
        ];
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'low_stock_threshold');
    }

    public function scopeInCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Helpers
    public function isLowStock(): bool
    {
        return $this->stock <= $this->low_stock_threshold;
    }

    public function decrementStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
    }

    public function incrementStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }
}