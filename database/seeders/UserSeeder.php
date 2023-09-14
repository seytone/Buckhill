<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(2)
            ->state(new Sequence(
                [
                    'first_name' => 'Buckhill',
                    'last_name' => 'Administrator',
                    'is_admin' => 1,
                    'email' => 'admin@buckhill.co.uk',
                    'password' => bcrypt('admin'),
                ],
                [
                    'first_name' => 'Jackson',
                    'last_name' => 'Echeverri',
                    'is_admin' => 1,
                    'email' => 'jecheverri@buckhill.co.uk',
                    'password' => bcrypt('jecheverri'),
                ]
            ))
            ->create();

        User::factory()->count(10)->create()->each(function ($user) {
            $user->orders()->saveMany(Order::factory()->count(5)->make());
        });
    }
}
