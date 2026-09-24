<?php

namespace Database\Factories;

use App\Enums\DealStage;
use App\Enums\DealStatus;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deal>
 */
class DealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->catchPhrase(),
            'value' => fake()->numberBetween(500, 50000),
            'stage' => fake()->randomElement(DealStage::cases()),
            'status' => DealStatus::Open,
            'expected_close_date' => fake()->dateTimeBetween('now', '+2 months'),
        ];
    }
}
