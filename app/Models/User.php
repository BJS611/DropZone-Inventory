<?php

namespace App\Models;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Traits\HasEnumValues;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasEnumValues;
    use HasFactory;
    use HasUuids;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Enum classes keyed by the guarded raw database columns.
     *
     * @var array<string, class-string<\BackedEnum>>
     */
    protected array $enums = [
        'role' => Role::class,
        'status' => UserStatus::class,
    ];

    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class, 'performed_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'created_by');
    }

    public function approvedBorrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'approved_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role === Role::STAFF;
    }

    public function isViewer(): bool
    {
        return $this->role === Role::VIEWER;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    /**
     * Permission check delegating to the Role enum.
     */
    public function can($abilities, $arguments = []): bool
    {
        if (is_string($abilities) && $this->role->can($abilities)) {
            return true;
        }

        return parent::can($abilities, $arguments);
    }
}
