<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'role_name' => 'USER', 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'role_name' => 'ADMIN', 'created_at' => null, 'updated_at' => null],
        ]);
    }
}