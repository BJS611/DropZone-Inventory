<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $target): bool
    {
        return $user->isAdmin() || $user->is($target);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $target): bool
    {
        return $user->isAdmin() || $user->is($target);
    }

    public function updateRole(User $user, User $target): bool
    {
        return $user->isAdmin();
    }

    public function deactivate(User $user, User $target): bool
    {
        return $user->isAdmin() && ! $target->is($user);
    }

    public function delete(User $user, User $target): bool
    {
        // Jangan biarkan aplikasi tanpa administrator.
        return $user->isAdmin()
            && ! $target->is($user)
            && $target->role === Role::VIEWER;
    }

    public function resetPassword(User $user, User $target): bool
    {
        return $user->isAdmin() && ! $target->is($user);
    }
}
