<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('order_items')->insert([
            [
                'id' => 1,
                'order_id' => 1,
                'product_id' => 2,
                'quantity' => 2,
                'created_at' => '2024-12-09 11:32:28',
                'updated_at' => '2024-12-09 11:32:28',
            ],
            [
                'id' => 2,
                'order_id' => 1,
                'product_id' => 6,
                'quantity' => 1,
                'created_at' => '2024-12-09 11:32:28',
                'updated_at' => '2024-12-09 11:32:28',
            ],
        ]);
    }
}