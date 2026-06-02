<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sale extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'subtotal'        => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'total_amount'    => 'decimal:2',
            'amount_tendered' => 'decimal:2',
            'change_amount'   => 'decimal:2',
            'voided_at'       => 'datetime',
        ];
    }

    // Auto-generate reference number on creation
    protected static function booted(): void
    {
        static::creating(function (Sale $sale) {
            if (blank($sale->reference)) {
                $sale->reference = 'TXN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
            }
        });
    }

    // Relationships
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeVoided($query)
    {
        return $query->where('status', 'voided');
    }

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('created_at', $date);
    }

    public function scopeForDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeForCashier($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helpers
    public function isVoided(): bool
    {
        return $this->status === 'voided';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}