<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@dropzone.test',
                'password' => 'password',
                'role' => Role::ADMIN,
                'status' => UserStatus::ACTIVE,
            ],
            [
                'name' => 'Staff Gudang',
                'email' => 'staff@dropzone.test',
                'password' => 'password',
                'role' => Role::STAFF,
                'status' => UserStatus::ACTIVE,
            ],
            [
                'name' => 'Viewer',
                'email' => 'viewer@dropzone.test',
                'password' => 'password',
                'role' => Role::VIEWER,
                'status' => UserStatus::ACTIVE,
            ],
        ];

        foreach ($users as $row) {
            User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'password' => $row['password'],
                    'role' => $row['role'],
                    'status' => $row['status'],
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
