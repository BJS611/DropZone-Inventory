<?php

namespace App\Models;

use App\Enums\ItemCondition;
use App\Traits\HasEnumValues;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingItem extends Model
{
    use HasEnumValues;
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'borrowing_id',
        'item_id',
        'quantity',
        'returned_quantity',
        'condition_before',
        'condition_after',
    ];

    protected array $enums = [
        'condition_before' => ItemCondition::class,
        'condition_after' => ItemCondition::class,
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'returned_quantity' => 'integer',
        ];
    }

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function remainingQuantity(): int
    {
        return $this->quantity - $this->returned_quantity;
    }

    public function isFullyReturned(): bool
    {
        return $this->remainingQuantity() <= 0;
    }
}
