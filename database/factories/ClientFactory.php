<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName() . ' ' . $this->faker->lastName();
        
        return [
            'dni' => $this->faker->unique()->numerify('########'),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => $firstName . ' ' . $lastName,
            'phone' => $this->faker->optional()->numerify('9########'),
            'email' => $this->faker->optional()->safeEmail(),
            'address' => $this->faker->optional()->address(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
} 