<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Infra\Uuid\UuidGenerator;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Player::factory(25)->create();

        Player::create([
            'id' => (new UuidGenerator())->generateUuid(), 
            'name' => fake()->name(),
            'hability' => rand(0,5),
            'is_goalkeeper' => fake()->boolean(),
        ]);
    }
}
