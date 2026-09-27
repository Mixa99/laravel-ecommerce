<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('orders')->insert([
            [
                'id' => 1,
                'user_id' => 2,
                'status' => 'NEMA',
                'date_of_delivery' => '2024-12-14',
                'total_price' => 264288,
                'created_at' => '2024-12-09 11:32:28',
                'updated_at' => '2024-12-09 11:32:28',
            ],
        ]);
    }
}