<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Latihan>
 */
class LatihanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_user' => $this->faker->randomNumber(),
            'id_bahasa' => $this->faker->randomNumber(),
            'id_kategori' => $this->faker->randomNumber(),
            'id_tingkat_kesulitan' => $this->faker->randomNumber(),
            'jumlah_kata' => $this->faker->randomNumber(),
            'jumlah_benar' => $this->faker->randomNumber(),
            'selesai' => $this->faker->boolean(),
        ];
    }
}
