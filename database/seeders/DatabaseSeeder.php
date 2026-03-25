<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\ProfitMarginTier;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Users and Authentication
            UserSeeder::class,
            
            // 2. Roles and Permissions
            RolesSeeder::class,
            
            // 3. Settings
            SettingsSeeder::class,
            
            // 4. Basic Configuration
            WarehouseSeeder::class,
            WarehousePermissionsSeeder::class,
            PurchasePermissionsSeeder::class,
            SalesPermissionsSeeder::class,
            ProfitMarginSeeder::class,
            
            // 5. Master Data
            CategorySeeder::class,
            SupplierSeeder::class,
            CustomerSeeder::class,
            
            // 6. Products
            ProductSeeder::class,
            InitialProductSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Users: ' . User::count());
        $this->command->info('   - Warehouses: ' . Warehouse::count());
        $this->command->info('   - Categories: ' . Category::count());
        $this->command->info('   - Products: ' . Product::count());
        $this->command->info('   - Suppliers: ' . Supplier::count());
        $this->command->info('   - Customers: ' . Customer::count());
        $this->command->info('');
        $this->command->info('🔐 Login credentials:');
        $this->command->info('   1. محمود حسن (info@bzzix.com) - Super Admin');
        $this->command->info('   2. ادم (adam@bzzix.com) - Cashier');
        $this->command->info('   3. فاطمة عوض (fatema@bzzix.com) - Cashier');
        $this->command->info('   Password: password');
    }
}
