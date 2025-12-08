<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // MAIN CATEGORIES
        $sparePartId = Str::uuid();
        $runningStoreId = Str::uuid();
        $repairId = Str::uuid();
        $othersId = Str::uuid();

        DB::table('categories')->insert([
            [
                'id' => $sparePartId,
                'name' => 'Spare Part',
                'code' => 'SP',
                'category_parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => $runningStoreId,
                'name' => 'Running Store',
                'code' => 'RS',
                'category_parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => $repairId,
                'name' => 'Repair',
                'code' => 'REPAIR',
                'category_parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => $othersId,
                'name' => 'Others',
                'code' => 'ETC',
                'category_parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // CHILD CATEGORIES - Running Store
        DB::table('categories')->insert([
            [
                'id' => Str::uuid(),
                'name' => 'Alat Tulis Kantor',
                'code' => 'ATK',
                'category_parent_id' => $runningStoreId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Cleaning',
                'code' => 'CLE',
                'category_parent_id' => $runningStoreId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Chemical',
                'code' => 'CHEM',
                'category_parent_id' => $runningStoreId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Packaging',
                'code' => 'PKG',
                'category_parent_id' => $runningStoreId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'General',
                'code' => 'GEN',
                'category_parent_id' => $runningStoreId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // CHILD CATEGORIES - Others
        DB::table('categories')->insert([
            [
                'id' => Str::uuid(),
                'name' => 'General',
                'code' => 'GEN',
                'category_parent_id' => $othersId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Sipil',
                'code' => 'CIVIL',
                'category_parent_id' => $othersId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Service',
                'code' => 'SVC',
                'category_parent_id' => $othersId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
