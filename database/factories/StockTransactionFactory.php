<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockTransaction>
 */
class StockTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'type' => fake()->randomElement(TransactionType::cases()),
            'quantity' => fake()->numberBetween(1, 20),
            'from_location_id' => null,
            'to_location_id' => null,
            'note' => fake()->optional()->sentence(),
            'performed_by' => User::factory(),
        ];
    }
}
