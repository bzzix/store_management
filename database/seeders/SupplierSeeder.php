<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating suppliers...');
        
        Supplier::create([
            'company_name' => 'الرحمة للأعلاف',
            'name' => 'علاء شريف',
            'phone' => '01062226955',
            'is_active' => true,
        ]);

        Supplier::create([
            'company_name' => 'أبو السيد للأعلاف',
            'name' => 'حماده الشاعر',
            'phone' => '01000698449',
            'is_active' => true,
        ]);
        
        $this->command->info('✅ Suppliers created');
    }
}
