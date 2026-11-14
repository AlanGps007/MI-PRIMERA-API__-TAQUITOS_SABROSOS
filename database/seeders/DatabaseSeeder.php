<?php

namespace Database\Seeders;

use App\Models\Taquito;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Taquito::firstOrCreate(
            ['name' => 'Taco al pastor'],
            [
                'description' => 'Con piña, cilantro y cebolla.',
                'price' => 25.00,
                'category' => 'Pastor',
            ],
        );

        Taquito::firstOrCreate(
            ['name' => 'Taco de suadero'],
            [
                'description' => 'Suadero suave con cilantro y cebolla.',
                'price' => 28.00,
                'category' => 'Suadero',
            ],
        );

        Taquito::firstOrCreate(
            ['name' => 'Taco de carnitas'],
            [
                'description' => 'Carnitas estilo Michoacan.',
                'price' => 30.00,
                'category' => 'Carnitas',
            ],
        );
    }
}
