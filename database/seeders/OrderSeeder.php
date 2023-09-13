<?php

namespace Database\Seeders;

use App\Models\Order;

use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory()->times(50)->create();
    }
}
