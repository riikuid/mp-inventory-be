<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('roles')->insert([
            [
                'name'        => 'Admin',
                'slug'        => 'admin',
                'description' => 'Full access, manage master data & users',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Storekeeper',
                'slug'        => 'storekeeper',
                'description' => 'Can label, edit units, assembly, view inventory',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Purchasing',
                'slug'        => 'purchasing',
                'description' => 'Can label, edit units, assembly, view inventory',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);
    }
}
