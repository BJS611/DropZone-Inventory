<?php

namespace App\Models;

use App\Enums\TransactionType;
use App\Traits\HasEnumValues;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    use HasEnumValues;
    use HasFactory;
    use HasUuids;

    public const UPDATED_AT = null;

    protected $fillable = [
        'item_id',
        'type',
        'quantity',
        'from_location_id',
        'to_location_id',
        'note',
        'performed_by',
    ];

    protected array $enums = [
        'type' => TransactionType::class,
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    protected static function booted(): void
    {
        static::updating(function (StockTransaction $transaction) {
            throw new \LogicException('Data transaksi tidak dapat diubah.');
        });

        static::deleting(function (StockTransaction $transaction) {
            throw new \LogicException('Data transaksi tidak dapat dihapus.');
        });
    }
}
