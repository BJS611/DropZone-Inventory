<?php

namespace Database\Factories;

use App\Enums\BorrowingStatus;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Borrowing>
 */
class BorrowingFactory extends Factory
{
    public function definition(): array
    {
        $borrowedAt = fake()->dateTimeBetween('-1 month');

        return [
            'borrower_name' => fake()->name(),
            'borrower_identifier' => fake()->optional()->numerify('EMP-####'),
            'borrower_contact' => fake()->optional()->phoneNumber(),
            'purpose' => fake()->optional()->sentence(),
            'borrowed_at' => $borrowedAt,
            'expected_return_at' => fake()->dateTimeBetween($borrowedAt, '+1 week'),
            'returned_at' => null,
            'status' => BorrowingStatus::BORROWED,
            'approved_by' => User::factory(),
            'created_by' => User::factory(),
            'note' => fake()->optional()->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BorrowingStatus::PENDING,
            'borrowed_at' => null,
        ]);
    }
}
