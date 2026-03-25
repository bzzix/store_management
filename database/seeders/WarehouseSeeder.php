<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create warehouses if not exist
        if (Warehouse::count() == 0) {
            Warehouse::create([
                'name' => 'المخزن الرئيسي',
                'code' => 'WH-001',
                'is_active' => true,
                'is_main' => true,
            ]);

            Warehouse::create([
                'name' => 'مخزن فرعي 1',
                'code' => 'WH-002',
                'is_active' => true,
            ]);

            $this->command->info('✅ Warehouses created');
        }
    }
}
