<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Tulis satu baris audit log.
     *
     * @param  string  $action  Nama aksi yang terjadi.
     * @param  string|null  $entity  Nama entitas yang terpengaruh.
     * @param  string|null  $entityId  ID entitas yang terpengaruh.
     * @param  array<string,mixed>|null  $metadata  Konteks tambahan (tidak boleh berisi rahasia).
     * @param  User|null  $user  Pelaku; default user yang sedang login.
     */
    public static function log(
        string $action,
        ?string $entity = null,
        ?string $entityId = null,
        ?array $metadata = null,
        ?User $user = null,
    ): AuditLog {
        $user ??= Auth::user();

        return AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
