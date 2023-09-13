<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['credit_card', 'cash_on_delivery', 'bank_transfer']);

        switch ($type) {
            case 'credit_card':
                $details = [
                    'holder_name' => fake()->name(),
                    'number' => fake()->creditCardNumber(),
                    'cvv' => fake()->randomNumber(3),
                    'expire_date' => fake()->creditCardExpirationDate(),
                ];
                break;
            case 'cash_on_delivery':
                $details = [
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'address' => fake()->address(),
                ];
                break;
            case 'bank_transfer':
                $details = [
                    'swift' => fake()->swiftBicNumber(),
                    'iban' => fake()->iban(),
                    'name' => fake()->name(),
                ];
                break;
            default:
                $details = [];
                break;
        }

        return [
            'uuid' => fake()->unique()->uuid(),
            'type' => $type,
            'details' => $details,
        ];
    }
}
