<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $roleId = 1;

        // Opsi B (Lebih Aman): Cari ID berdasarkan nama role
        $role = Role::where('name', 'Admin')->first();
        $roleId = $role ? $role->id : 1;

        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'], // Kunci pencarian (agar tidak duplikat)
            [
                'name'      => 'Super Administrator',
                'password'  => Hash::make('password'), // Ganti dengan password yang kuat
                'role_id'   => $roleId,
                // Kolom tambahan sesuai konteks sebelumnya (jika tidak auto-generated di Model/Observer)
                // 'initials'  => 'SA',
            ]
        );
    }
}
