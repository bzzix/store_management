<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InitialProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating initial products...');

        $warehouse = Warehouse::first() ?: Warehouse::create(['name' => 'المخزن الرئيسي', 'code' => 'MAIN', 'is_active' => true]);
        
        $catPoultry = Category::where('name', 'أعلاف دواجن')->first() ?: Category::create(['name' => 'أعلاف دواجن', 'slug' => 'poultry-feed']);
        $catLivestock = Category::where('name', 'أعلاف ماشية')->first() ?: Category::create(['name' => 'أعلاف ماشية', 'slug' => 'livestock-feed']);

        $products = [
            // Kabo
            ['name' => 'كابو محبب 25', 'price' => 585, 'weight' => 25, 'category' => $catPoultry],
            ['name' => 'كابو محبب 50', 'price' => 1165, 'weight' => 50, 'category' => $catPoultry],
            ['name' => 'كابو ناعم 25', 'price' => 590, 'weight' => 25, 'category' => $catPoultry],
            ['name' => 'كابو ناعم 50', 'price' => 1180, 'weight' => 50, 'category' => $catPoultry],
            
            // Al-Anani
            ['name' => 'العناني محبب 25', 'price' => 595, 'weight' => 25, 'category' => $catPoultry],
            ['name' => 'العناني محبب 50', 'price' => 1190, 'weight' => 50, 'category' => $catPoultry],
            ['name' => 'العناني ناعم 25', 'price' => 600, 'weight' => 25, 'category' => $catPoultry],
            ['name' => 'العناني ناعم 50', 'price' => 1200, 'weight' => 50, 'category' => $catPoultry],
            
            // Glory
            ['name' => 'جلوري محبب 25', 'price' => 595, 'weight' => 25, 'category' => $catPoultry],
            ['name' => 'جلوري محبب 50', 'price' => 1160, 'weight' => 50, 'category' => $catPoultry],
            ['name' => 'جلوري ناعم 25', 'price' => 600, 'weight' => 25, 'category' => $catPoultry],
            ['name' => 'جلوري ناعم 50', 'price' => 1200, 'weight' => 50, 'category' => $catPoultry],
            
            // Radda
            ['name' => 'ردة الجمل', 'price' => 595, 'weight' => 25, 'category' => $catLivestock],
            ['name' => 'ردة مطاحن', 'price' => 570, 'weight' => 25, 'category' => $catLivestock],
        ];

        foreach ($products as $item) {
            $slug = Str::slug($item['name'], '-', 'ar');
            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $item['category']->id,
                    'warehouse_id' => $warehouse->id,
                    'name' => $item['name'],
                    'current_cost_price' => $item['price'], // Assumption for cost
                    'current_base_price' => $item['price'],
                    'base_unit' => 'شكارة',
                    'weight' => $item['weight'],
                    'stock_quantity' => 100, // Initial stock
                    'min_stock_level' => 10,
                    'track_inventory' => true,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ Initial products created successfully');
    }
}
