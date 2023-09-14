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

        $status = OrderStatus::all()->random();
        $payment = ($status->title == 'paid' || $status->title == 'shipped') ? Payment::all()->random()->id : null;

        return [
            'user_id' => User::factory(),
            'order_status_id' => $status->id,
            'payment_id' => $payment,
            'uuid' => fake()->unique()->uuid(),
            'products' => $products,
            'address' => [
                'billing' => fake()->address(),
                'shipping' => fake()->address(),
            ],
            'delivery_fee' => fake()->randomFloat(2, 0, 10),
            'amount' => fake()->randomFloat(2, 20, 1000),
            'shipped_at' => $status->title == 'shipped' ? fake()->dateTimeThisYear() : null,
        ];
    }
}
