<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 2,
                'role_id' => 2,
                'name' => 'Mina',
                'email' => 'mina@gmail.com',
                'password' => '$2y$12$dCkxwSp8hpXfpTOiKZnuAe3jzHM4FpTwA0zYrGMZ.FHKU9hEOw/16',
                'remember_token' => null,
                'created_at' => '2024-12-09 11:16:56',
                'updated_at' => '2024-12-09 11:16:56',
            ],
            [
                'id' => 3,
                'role_id' => 1,
                'name' => 'Mihajlo',
                'email' => 'mihajlo@gmail.com',
                'password' => '$2y$12$.Zuc7TfuG/rNeJ.52spwXOuJxIEo9J4RA/KN0lR4HYF2d/GuOwELG',
                'remember_token' => null,
                'created_at' => '2024-12-09 12:12:02',
                'updated_at' => '2024-12-09 12:12:02',
            ],
        ]);
    }
}