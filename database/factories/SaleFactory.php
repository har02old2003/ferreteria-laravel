<?php

namespace Database\Factories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->numerify('VTA-#####'),
            'total' => 0, // Se actualizará después de agregar productos
            'status' => fake()->randomElement(['pending', 'completed', 'cancelled']),
            'payment_method' => fake()->randomElement(['cash', 'card', 'transfer']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
} 