<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * إضافة صلاحيات المشتريات للمشاريع الموجودة
 * تشغيل: php artisan db:seed --class=PurchasePermissionsSeeder
 */
class PurchasePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            ['name' => 'purchases_view', 'display_name' => 'عرض المشتريات', 'display_name_en' => 'View purchases'],
            ['name' => 'purchases_add', 'display_name' => 'إضافة مشتريات', 'display_name_en' => 'Add purchases'],
            ['name' => 'purchases_update', 'display_name' => 'تعديل المشتريات', 'display_name_en' => 'Update purchases'],
            ['name' => 'purchases_delete', 'display_name' => 'حذف المشتريات', 'display_name_en' => 'Delete purchases'],
            ['name' => 'purchases_print', 'display_name' => 'طباعة المشتريات', 'display_name_en' => 'Print purchases'],
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

        // الكاشير عادة لا يرى المشتريات، لكن يمكن إضافتها إذا طلب العميل

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
