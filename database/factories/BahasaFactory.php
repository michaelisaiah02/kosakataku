<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\bahasa>
 */
class BahasaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bahasa' => $this->faker->word,
            'kode_deepl' => $this->faker->word,
            'kode_google' => $this->faker->word,
        ];
    }
}
