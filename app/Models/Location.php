<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Location $location) {
            if ($location->items()->exists()) {
                throw new \LogicException(
                    'Lokasi tidak dapat dihapus karena masih digunakan oleh barang inventaris.'
                );
            }
        });
    }
}
