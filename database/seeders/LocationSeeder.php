<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\Section;
use App\Models\Warehouse;
use App\Models\Rack;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan transaction agar data terjamin konsistensinya (All or Nothing)
        DB::transaction(function () {

            // ==========================================
            // 1. SEED DEPARTMENTS
            // ==========================================
            $departmentsData = [
                ['code' => 'BOD', 'name' => 'Board Of Directors'],
                ['code' => 'CSB', 'name' => 'CS/Baking'],
                ['code' => 'FAC', 'name' => 'Finance & Accounting'],
                ['code' => 'HSE', 'name' => 'Health Safety & Environmental'],
                ['code' => 'MFS', 'name' => 'Management'],
                ['code' => 'MKT', 'name' => 'Marketing & Promotion'],
                ['code' => 'MR/FSTL', 'name' => 'Management Representative/Food Safety Team Leader'],
                ['code' => 'PCH', 'name' => 'Purchasing'],
                ['code' => 'PGA', 'name' => 'Personil & General Affair'],
                ['code' => 'PRO', 'name' => 'Production'],
                ['code' => 'QCL', 'name' => 'QC/Laboratory'],
                ['code' => 'S&W', 'name' => 'Shipping & Warehousing'],
                ['code' => 'TEC', 'name' => 'Technical'],
                ['code' => 'IT',  'name' => 'IT Jakarta'],
                ['code' => 'GM',  'name' => 'General Manager'],
                ['code' => 'MLEP', 'name' => 'MILL LINE C EXPANSION PROJECT'],
                ['code' => 'BDC', 'name' => 'Bisnis Development'],
            ];

            foreach ($departmentsData as $dept) {
                Department::firstOrCreate(
                    ['code' => $dept['code']],
                    ['name' => $dept['name']]
                );
            }

            // ==========================================
            // 2. SEED SECTIONS
            // ==========================================
            // Format: [Dept Code, Section Name, Section Alias]
            $sectionsData = [
                ['TEC', 'Electric', 'E'],
                ['S&W', 'Shipping & Delivery', 'S'],
                ['QCL', 'QC/Laboratory', 'Q'],
                ['PRO', 'Packing', 'P'],
                ['PGA', 'Personel External Affair', 'P'],
                ['PCH', 'Purchasing', 'P'],
                ['MKT', 'Marketing Cilacap', 'M'],
                ['MFS', 'Management', 'M'],
                ['HSE', 'Health & Safety', 'H'],
                ['FAC', 'Finance & Accounting', 'F'],
                ['CSB', 'CS/Baking', 'C'],
                ['TEC', 'Maintenance', 'M'],
                ['TEC', 'Utility', 'U'],
                ['TEC', 'Maintenance/Civil', 'S'],
                ['S&W', 'Warehousing', 'W'],
                ['PRO', 'Silo', 'S'],
                ['PRO', 'Mill', 'M'],
                ['PGA', 'Personel Internal Affair', 'P'],
                ['HSE', 'Environment', 'E'],
                ['MKT', 'Marketing Solo', 'M'],
                ['MFS', 'MR FSTL', 'M'],
                ['BOD', 'Board Of Directors', 'B'],
                ['IT',  'IT Jakarta', 'I'],
                ['GM',  'GM', 'G'],
                ['MLEP', 'Mill Line C Expansion Project', 'MLEP'],
            ];

            // Ambil referensi semua department untuk lookup ID
            $departments = Department::all()->keyBy('code');

            foreach ($sectionsData as $data) {
                $deptCode = $data[0];
                $sectName = $data[1];
                $sectCode = $data[2];

                if (isset($departments[$deptCode])) {
                    Section::firstOrCreate(
                        [
                            'department_id' => $departments[$deptCode]->id,
                            'name' => $sectName
                        ],
                        [
                            'code' => $sectCode,
                            'department_code' => $deptCode // Sesuai kolom denormalisasi Anda
                        ]
                    );
                }
            }

            // ==========================================
            // 3. SEED WAREHOUSES
            // ==========================================
            $warehouseNames = [
                'Gudang Bahan Baku (Raw Material)',
                'Gudang Barang Jadi (Finished Goods)',
                'Gudang Sparepart',
                'Gudang Karantina',
            ];

            $createdWarehouses = [];
            foreach ($warehouseNames as $name) {
                $createdWarehouses[] = Warehouse::firstOrCreate(['name' => $name]);
            }

            // ==========================================
            // 4. SEED RACKS
            // ==========================================
            // Membuat Rak A-E di setiap gudang
            foreach ($createdWarehouses as $wh) {
                foreach (range('A', 'E') as $char) {
                    Rack::firstOrCreate([
                        'warehouse_id' => $wh->id,
                        'name' => "Rak {$char}"
                    ]);
                }
            }

            // ==========================================
            // 5. RELASI SECTION -> WAREHOUSES
            // ==========================================
            // Skenario: Bagian "Warehousing" (S&W) mengelola semua gudang fisik ini.

            $warehousingSection = Section::where('name', 'Warehousing')
                ->where('department_code', 'S&W')
                ->first();

            if ($warehousingSection) {
                // Ambil ID dari semua warehouse yang baru dibuat/diambil
                $warehouseIds = collect($createdWarehouses)->pluck('id');

                // Sync memastikan tidak ada duplikasi relasi
                $warehousingSection->warehouses()->syncWithoutDetaching($warehouseIds);
            }
        });
    }
}
