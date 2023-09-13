<?php

namespace Database\Seeders;

use App\Models\OrderStatus;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderStatus::factory()
            ->count(5)
            ->state(new Sequence(
                [
                    'uuid' => fake()->uuid(),
                    'title' => 'open'
                ],
                [
                    'uuid' => fake()->uuid(),
                    'title' => 'pending payment'
                ],
                [
                    'uuid' => fake()->uuid(),
                    'title' => 'paid'
                ],
                [
                    'uuid' => fake()->uuid(),
                    'title' => 'shipped'
                ],
                [
                    'uuid' => fake()->uuid(),
                    'title' => 'canceled'
                ],
            ))
            ->create();
    }
}
