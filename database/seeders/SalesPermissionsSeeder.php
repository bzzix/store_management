<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * إضافة صلاحيات المبيعات للمشاريع الموجودة
 * تشغيل: php artisan db:seed --class=SalesPermissionsSeeder
 */
class SalesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            ['name' => 'sales_view', 'display_name' => 'عرض المبيعات', 'display_name_en' => 'View sales'],
            ['name' => 'sales_add', 'display_name' => 'إضافة مبيعات', 'display_name_en' => 'Add sales'],
            ['name' => 'sales_update', 'display_name' => 'تعديل المبيعات', 'display_name_en' => 'Update sales'],
            ['name' => 'sales_delete', 'display_name' => 'حذف المبيعات', 'display_name_en' => 'Delete sales'],
            ['name' => 'sales_cancel', 'display_name' => 'إلغاء فواتير المبيعات', 'display_name_en' => 'Cancel sales'],
            ['name' => 'sales_print', 'display_name' => 'طباعة فواتير المبيعات', 'display_name_en' => 'Print sales'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                ['display_name' => $perm['display_name'], 'display_name_en' => $perm['display_name_en']]
            );
        }

        // إسناد الصلاحيات للمدير والمدير العام
        $adminRoles = Role::whereIn('name', ['admin', 'super_admin'])->get();
        foreach ($adminRoles as $role) {
            $role->givePermissionTo(array_column($permissions, 'name'));
        }

        // إسناد الصلاحيات الأساسية للكاشير
        $cashierRole = Role::where('name', 'cashier')->first();
        if ($cashierRole) {
            $cashierRole->givePermissionTo(['sales_view', 'sales_add', 'sales_print']);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
