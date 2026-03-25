<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SaleMethod;
use App\Models\ProfitMarginTier;
use Illuminate\Support\Facades\DB;

class ProfitMarginTierSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ طرق البيع
            |--------------------------------------------------------------------------
            */
            $cash = SaleMethod::create([
                'name' => 'كاش',
                'code' => 'cash',
                'priority' => 3,
                'is_active' => true,
            ]);

            $installment = SaleMethod::create([
                'name' => 'قسط',
                'code' => 'installment',
                'priority' => 2,
                'is_active' => true,
            ]);

            $credit = SaleMethod::create([
                'name' => 'آجل',
                'code' => 'credit',
                'priority' => 1,
                'is_active' => true,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ الشرائح السعرية
            |--------------------------------------------------------------------------
            */
            $tiers = [
                [
                    'data' => [
                        'name' => 'حتى 300',
                        'min_value' => 0,
                        'max_value' => 300,
                        'priority' => 4,
                        'is_active' => true,
                    ],
                    'profits' => [
                        'cash' => 10,
                        'installment' => 20,
                        'credit' => 80,
                    ],
                ],
                [
                    'data' => [
                        'name' => 'من 301 إلى 700',
                        'min_value' => 301,
                        'max_value' => 700,
                        'priority' => 3,
                        'is_active' => true,
                    ],
                    'profits' => [
                        'cash' => 15,
                        'installment' => 30,
                        'credit' => 120,
                    ],
                ],
                [
                    'data' => [
                        'name' => 'من 701 إلى 1100',
                        'min_value' => 701,
                        'max_value' => 1100,
                        'priority' => 2,
                        'is_active' => true,
                    ],
                    'profits' => [
                        'cash' => 20,
                        'installment' => 40,
                        'credit' => 160,
                    ],
                ],
                [
                    'data' => [
                        'name' => 'أكبر من 1100',
                        'min_value' => 1101,
                        'max_value' => null,
                        'priority' => 1,
                        'is_active' => true,
                    ],
                    'profits' => [
                        'cash' => 25,
                        'installment' => 50,
                        'credit' => 200,
                    ],
                ],
            ];

            /*
            |--------------------------------------------------------------------------
            | 3️⃣ إدخال الشرائح وربط الأرباح
            |--------------------------------------------------------------------------
            */
            foreach ($tiers as $tierData) {

                $tier = ProfitMarginTier::create($tierData['data']);

                foreach ($tierData['profits'] as $methodCode => $profitValue) {

                    $method = SaleMethod::where('code', $methodCode)->first();

                    $tier->saleMethods()->attach($method->id, [
                        'profit_value' => $profitValue,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }
}
