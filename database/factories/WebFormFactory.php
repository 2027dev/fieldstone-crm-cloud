<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WebForm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WebForm>
 */
class WebFormFactory extends Factory
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
            'name' => 'Contact us',
            'headline' => 'Talk to our team',
        ];
    }
}
