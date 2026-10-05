<?php

namespace Database\Factories;

use App\Enums\ItemCondition;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BorrowingItem>
 */
class BorrowingItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'borrowing_id' => Borrowing::factory(),
            'item_id' => Item::factory(),
            'quantity' => fake()->numberBetween(1, 5),
            'returned_quantity' => 0,
            'condition_before' => ItemCondition::GOOD,
            'condition_after' => null,
        ];
    }
}
