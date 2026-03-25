<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating categories...');
        
        $categories = [
            'اعلاف دواجن',
            'أعلاف ماشية',
            'خلطة دواجن',
            'خلطة ماشية',
            'علافات',
            'سقايات',
            'كتاكيت',
            'دواجن',
            'أدوية',
            'مبيدات',
            'مخصبات زراعية',
            'أسمدة (كيماوي أزوت)',
            'تقاوي وبذور',
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
                'slug' => \Str::slug($categoryName) ?: str_replace([' ', '(', ')'], ['-', '', ''], $categoryName),
                'is_active' => true,
                'sort_order' => 1,
            ]);
        }

        $this->command->info('✅ Categories created');
    }
}
