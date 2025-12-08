<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Timken',
            'SKF',
            'NKN',
            'NSK',
            'NTN',
            'FAG',
            'Nachi',
        ];

        foreach ($brands as $name) {
            Brand::firstOrCreate(['name' => $name], []);
        }
    }
}
