<?php

namespace Database\Factories;

use App\Models\Email;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Email>
 */
class EmailFactory extends Factory
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
            'folder' => 'inbox',
            'from_name' => fake()->name(),
            'from_email' => fake()->safeEmail(),
            'to_email' => fake()->safeEmail(),
            'subject' => fake()->sentence(5),
            'body' => fake()->paragraphs(2, true),
        ];
    }
}
