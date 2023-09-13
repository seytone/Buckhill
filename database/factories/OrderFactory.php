<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\OrderStatus;
use App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 20);
        $products = [];

        for ($i = 0; $i < $quantity; $i++) {
            $products[] = [
                'product' => fake()->uuid(),
                'quantity' => fake()->numberBetween(1, 10),
            ];
        }

        return [
            'user_id' => User::factory(),
            'order_status_id' => OrderStatus::factory(),
            'payment_id' => Payment::factory(),
            'uuid' => fake()->unique()->uuid(),
            'products' => $products,
            'address' => [
                'billing' => fake()->address(),
                'shipping' => fake()->address(),
            ],
            'delivery_fee' => fake()->randomFloat(2, 0, 100),
            'amount' => fake()->randomFloat(2, 0, 100),
            'shipped_at' => fake()->dateTime(),
        ];
    }
}
