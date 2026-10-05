<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
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

    public function itemsCount(): int
    {
        return $this->items()->count();
    }

    protected static function booted(): void
    {
        static::deleting(function (Category $category) {
            if ($category->items()->exists()) {
                throw new \LogicException(
                    'Kategori tidak dapat dihapus karena masih digunakan oleh barang inventaris.'
                );
            }
        });
    }
}
