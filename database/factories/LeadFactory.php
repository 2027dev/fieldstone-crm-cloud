<?php

namespace Database\Factories;

use App\Enums\LeadSource;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
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
            'title' => fake()->company().' lead',
            'value' => fake()->numberBetween(1000, 20000),
            'source' => LeadSource::Manual,
        ];
    }
}
