<?php

namespace App\Models;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Enums\Unit;
use App\Traits\HasEnumValues;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasEnumValues;
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'category_id',
        'location_id',
        'supplier_id',
        'quantity',
        'minimum_stock',
        'unit',
        'condition',
        'status',
        'image_url',
    ];

    protected array $enums = [
        'unit' => Unit::class,
        'condition' => ItemCondition::class,
        'status' => ItemStatus::class,
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'minimum_stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function borrowingItems(): HasMany
    {
        return $this->hasMany(BorrowingItem::class);
    }

    /**
     * Stok yang sedang dipinjam (belum dikembalikan).
     */
    public function borrowedQuantity(): int
    {
        return (int) $this->borrowingItems()
            ->whereColumn('returned_quantity', '<', 'quantity')
            ->sum(\DB::raw('quantity - returned_quantity'));
    }

    public function availableQuantity(): int
    {
        return $this->quantity - $this->borrowedQuantity();
    }

    public function stockStatus(): string
    {
        if ($this->quantity === 0) {
            return 'OUT_OF_STOCK';
        }

        if ($this->quantity <= $this->minimum_stock) {
            return 'LOW';
        }

        return 'NORMAL';
    }

    public function isOutOfStock(): bool
    {
        return $this->stockStatus() === 'OUT_OF_STOCK';
    }

    public function isLowStock(): bool
    {
        return $this->stockStatus() === 'LOW';
    }

    /**
     * Scope: stok di bawah atau sama dengan minimum (masih ada).
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('quantity', '<=', 'minimum_stock')
            ->where('quantity', '>', 0);
    }

    /**
     * Scope: stok habis.
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('quantity', 0);
    }
}
