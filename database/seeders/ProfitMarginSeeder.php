<?php

namespace Database\Seeders;

use App\Models\ProfitMarginTier;
use App\Models\SaleMethod;
use App\Models\ProfitMarginTierMethod;
use Illuminate\Database\Seeder;

class ProfitMarginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create profit margin tiers if not exist
        if (ProfitMarginTier::count() == 0) {
            $tier1 = ProfitMarginTier::create([
                'name' => 'شريحة 1 (0-100)',
                'min_value' => 0,
                'max_value' => 100,
                'priority' => 1,
                'is_active' => true,
            ]);

            $tier2 = ProfitMarginTier::create([
                'name' => 'شريحة 2 (100-500)',
                'min_value' => 100,
                'max_value' => 500,
                'priority' => 2,
                'is_active' => true,
            ]);

            $tier3 = ProfitMarginTier::create([
                'name' => 'شريحة 3 (500+)',
                'min_value' => 500,
                'max_value' => null,
                'priority' => 3,
                'is_active' => true,
            ]);

            $this->command->info('✅ Profit margin tiers created');

            // Create sale methods if not exist
            if (SaleMethod::count() == 0) {
                $cash = SaleMethod::create([
                    'name' => 'نقدي',
                    'code' => 'cash',
                    'is_active' => true,
                ]);

                $installment = SaleMethod::create([
                    'name' => 'تقسيط',
                    'code' => 'installment',
                    'is_active' => true,
                ]);

                $credit = SaleMethod::create([
                    'name' => 'آجل',
                    'code' => 'credit',
                    'is_active' => true,
                ]);

                $this->command->info('✅ Sale methods created');

                // Create profit margins for each tier and method
                foreach ([$tier1, $tier2, $tier3] as $index => $tier) {
                    // Cash: 20%, 15%, 10%
                    ProfitMarginTierMethod::create([
                        'profit_margin_tier_id' => $tier->id,
                        'sale_method_id' => $cash->id,
                        'profit_value' => [20, 15, 10][$index],
                    ]);

                    // Installment: 30%, 25%, 20%
                    ProfitMarginTierMethod::create([
                        'profit_margin_tier_id' => $tier->id,
                        'sale_method_id' => $installment->id,
                        'profit_value' => [30, 25, 20][$index],
                    ]);

                    // Credit: 25%, 20%, 15%
                    ProfitMarginTierMethod::create([
                        'profit_margin_tier_id' => $tier->id,
                        'sale_method_id' => $credit->id,
                        'profit_value' => [25, 20, 15][$index],
                    ]);
                }

                $this->command->info('✅ Profit margin methods created');
            }
        }
    }
}
