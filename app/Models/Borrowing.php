<?php

namespace App\Models;

use App\Enums\BorrowingStatus;
use App\Traits\HasEnumValues;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Borrowing extends Model
{
    use HasEnumValues;
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'borrower_name',
        'borrower_identifier',
        'borrower_contact',
        'purpose',
        'borrowed_at',
        'expected_return_at',
        'returned_at',
        'status',
        'approved_by',
        'created_by',
        'note',
    ];

    protected array $enums = [
        'status' => BorrowingStatus::class,
    ];

    protected function casts(): array
    {
        return [
            'borrowed_at' => 'datetime',
            'expected_return_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(BorrowingItem::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function totalQuantity(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function totalReturnedQuantity(): int
    {
        return (int) $this->items()->sum('returned_quantity');
    }

    public function isFullyReturned(): bool
    {
        return $this->items()->exists()
            && ! $this->items()->get()->contains(fn ($item) => $item->returned_quantity < $item->quantity);
    }

    public function isOverdue(): bool
    {
        $terminal = [BorrowingStatus::RETURNED, BorrowingStatus::CANCELLED];

        return ! in_array($this->status, $terminal, true)
            && $this->expected_return_at !== null
            && $this->expected_return_at < Carbon::now();
    }

    protected static function booted(): void
    {
        static::deleting(function (Borrowing $borrowing) {
            if ($borrowing->status !== BorrowingStatus::CANCELLED) {
                throw new \LogicException(
                    'Data peminjaman tidak dapat dihapus kecuali dibatalkan.'
                );
            }
        });
    }
}
