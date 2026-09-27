<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('comments')->insert([
            [
                'id' => 3,
                'product_id' => 2,
                'user_id' => 2,
                'rating' => 'Vrlo dobar',
                'body' => 'Proizvod je previse, skup ali kvalitetan',
                'created_at' => '2024-12-09 11:29:24',
                'updated_at' => '2024-12-09 11:29:24',
            ],
        ]);
    }
}