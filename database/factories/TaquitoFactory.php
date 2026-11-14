<?php

namespace Database\Factories;

use App\Models\Taquito;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Taquito>
 */
class TaquitoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Taco al pastor',
                'Taco de suadero',
                'Taco de carnitas',
                'Taco de bistec',
            ]),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 15, 50),
            'category' => fake()->randomElement(['Pastor', 'Suadero', 'Carnitas', 'Bistec']),
            'is_available' => true,
        ];
    }
}
