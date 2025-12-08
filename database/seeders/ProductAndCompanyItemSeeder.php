<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CompanyItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductAndCompanyItemSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan kategori "Spare Part" sudah ada (dari CategorySeeder)
        $sparePartCategory = Category::firstOrCreate(
            ['name' => 'Spare Part'],
            [
                'code' => 'SP',
                'category_parent_id' => null,
            ]
        );

        /**
         * 1. Product: Bearing
         */
        $bearing = Product::firstOrCreate(
            [
                'name' => 'Bearing',
            ],
            [
                'category_id'      => $sparePartCategory->id,
                'machine_purchase' => null,
                'description'      => 'Berbagai jenis bearing untuk kebutuhan mesin.',
            ]
        );



        /**
         * 3. Product: Forklift Wheel
         */
        $forkliftWheel = Product::firstOrCreate(
            [
                'name' => 'Forklift Wheel',
            ],
            [
                'category_id'      => $sparePartCategory->id,
                'machine_purchase' => null,
                'description'      => 'Forklift wheel untuk kebutuhan forklift.',
            ]
        );

        /**
         * 4. Company items untuk Bearing
         *    Contoh kode internal: TEC-M-SP-0027 s/d 0031
         */
        $bearingCodes = [
            'TEC-M-SP-0027',
            'TEC-M-SP-0028',
            'TEC-M-SP-0029',
            'TEC-M-SP-0030',
            'TEC-M-SP-0031',
        ];

        foreach ($bearingCodes as $code) {
            CompanyItem::firstOrCreate(
                [
                    'product_id'   => $bearing->id,
                    'company_code' => $code,
                ],
                [
                    'default_rack_id' => null,
                    'specification'   => null,
                    'notes'           => null,
                ]
            );
        }

        /**
         * 5. Company items untuk Forklift Wheel
         */
        $forkliftWheelCodes = [
            'TEC-U-SP-0028',
            'TEC-U-SP-0029',
            'TEC-U-SP-0030',
        ];

        foreach ($forkliftWheelCodes as $code) {
            CompanyItem::firstOrCreate(
                [
                    'product_id'   => $forkliftWheel->id,
                    'company_code' => $code,
                ],
                [
                    'default_rack_id' => null,
                    'specification'   => null,
                    'notes'           => null,
                ]
            );
        }

        /**
         * 2. Product: Limit Switch
         */
        $limitSwitch = Product::firstOrCreate(
            [
                'name' => 'Limit Switch',
            ],
            [
                'category_id'      => $sparePartCategory->id,
                'machine_purchase' => null,
                'description'      => 'Limit switch untuk kontrol posisi / safety.',
            ]
        );

        /**
         * 6. Company item untuk Limit Switch: 0276
         */
        CompanyItem::firstOrCreate(
            [
                'product_id'   => $limitSwitch->id,
                'company_code' => '0276',
            ],
            [
                'default_rack_id' => null,
                'specification'   => null,
                'notes'           => 'Limit switch single item (bisa diubah nanti).',
            ]
        );
    }
}
