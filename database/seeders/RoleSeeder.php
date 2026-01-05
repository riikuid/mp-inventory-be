<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Full access, manage master data & users',
        ]);

        Role::create([
            'name' => 'Storekeeper',
            'slug' => 'storekeeper',
            'description' => 'Can label, edit units, assembly, view inventory',
        ]);

        Role::create([
            'name' => 'Purchasing',
            'slug' => 'purchasing',
            'description' => 'Can label, edit units, assembly, view inventory',
        ]);
    }
}
