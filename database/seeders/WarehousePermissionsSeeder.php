<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * إضافة صلاحيات المخازن للمشاريع الموجودة
 * تشغيل: php artisan db:seed --class=WarehousePermissionsSeeder
 */
class WarehousePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            ['name' => 'warehouses_view', 'display_name' => 'عرض المخازن', 'display_name_en' => 'View warehouses'],
            ['name' => 'warehouses_add', 'display_name' => 'إضافة مخازن', 'display_name_en' => 'Add warehouses'],
            ['name' => 'warehouses_update', 'display_name' => 'تعديل المخازن', 'display_name_en' => 'Update warehouses'],
            ['name' => 'warehouses_delete', 'display_name' => 'حذف المخازن', 'display_name_en' => 'Delete warehouses'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                ['display_name' => $perm['display_name'], 'display_name_en' => $perm['display_name_en']]
            );
        }

        // إسناد صلاحيات المخازن للمدير والمدير العام
        $adminRoles = Role::whereIn('name', ['admin', 'super_admin'])->get();
        foreach ($adminRoles as $role) {
            $role->givePermissionTo(array_column($permissions, 'name'));
        }

        // إسناد صلاحية العرض للكاشير
        $cashierRole = Role::where('name', 'cashier')->first();
        if ($cashierRole) {
            $cashierRole->givePermissionTo('warehouses_view');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
