<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Http\Requests\ResetUserPasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(private AuditService $audit) {}

    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('users.index', ['users' => $users]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validatedData();

        $user = \DB::transaction(function () use ($data, $request): User {
            $user = User::create($data);

            $this->audit->log(
                'user_create',
                'User',
                $user->id,
                ['name' => $user->name, 'email' => $user->email, 'role' => $user->role->value],
                $request->user(),
            );

            return $user;
        });

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', ['user' => $user]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $oldRole = $user->role;

        \DB::transaction(function () use ($user, $data, $oldRole, $request): void {
            $user->update($data);

            if (isset($data['role']) && $data['role'] !== $oldRole->value) {
                $this->audit->log(
                    'role_change',
                    'User',
                    $user->id,
                    ['from' => $oldRole->value, 'to' => $data['role']],
                    $request->user(),
                );
            }

            $this->audit->log(
                'user_update',
                'User',
                $user->id,
                ['name' => $user->name],
                $request->user(),
            );
        });

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, User $user)
    {
        $this->authorize('deactivate', $user);

        \DB::transaction(function () use ($user, $request): void {
            $next = $user->status === UserStatus::ACTIVE
                ? UserStatus::INACTIVE
                : UserStatus::ACTIVE;

            if ($next === UserStatus::INACTIVE && User::where('role', Role::ADMIN)->where('status', UserStatus::ACTIVE)->count() <= 1 && $user->role === Role::ADMIN) {
                throw ValidationException::withMessages([
                    'status' => 'Tidak dapat menonaktifkan administrator terakhir.',
                ]);
            }

            $user->update(['status' => $next->value]);

            $this->audit->log(
                'user_status_change',
                'User',
                $user->id,
                ['status' => $next->value],
                $request->user(),
            );
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'Status user berhasil diubah menjadi '.$user->status->value.'.');
    }

    public function resetPassword(ResetUserPasswordRequest $request, User $user)
    {
        \DB::transaction(function () use ($user, $request): void {
            $user->update(['password' => Hash::make($request->string('password')->toString())]);

            $this->audit->log(
                'user_password_reset',
                'User',
                $user->id,
                null,
                $request->user(),
            );
        });

        return redirect()->route('users.index')->with('success', 'Password user berhasil direset.');
    }
}
