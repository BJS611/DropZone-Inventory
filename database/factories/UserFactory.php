<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => Role::VIEWER,
            'status' => UserStatus::ACTIVE,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::ADMIN]);
    }

    public function staff(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::STAFF]);
    }

    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::VIEWER]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['status' => UserStatus::INACTIVE]);
    }
}
