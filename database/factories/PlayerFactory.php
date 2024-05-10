<?php

namespace Database\Factories;

use App\Infra\Uuid\UuidGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (new UuidGenerator())->generateUuid(),
            'name' => fake()->name(),
            'hability' => rand(0,5),
            'is_goalkeeper' => fake()->boolean(),
        ];
    }
}
