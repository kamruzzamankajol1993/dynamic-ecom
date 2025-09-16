<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word.rand(1, 100000),
            'code' => $this->faker->unique()->lexify('??'), // Generates a 2-letter code
            'status' => 1, // Default to an active status.
        ];
    }
}
