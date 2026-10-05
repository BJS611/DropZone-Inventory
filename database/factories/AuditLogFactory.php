<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        $actions = ['item_create', 'item_update', 'item_delete', 'stock_in', 'stock_out'];

        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement($actions),
            'entity' => fake()->randomElement(['Item', 'Borrowing', 'User']),
            'entity_id' => fake()->uuid(),
            'metadata' => null,
            'created_at' => fake()->dateTimeBetween('-1 month'),
        ];
    }
}
