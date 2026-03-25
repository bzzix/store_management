<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * RolesSeeder
 * 
 * ملف السيدر المسؤول عن إنشاء الأدوار والصلاحيات والمستخدمين الافتراضيين
 * 
 * الأدوار المُنشأة:
 * - super_admin: المدير العام (جميع الصلاحيات)
 * - admin: مدير النظام (جميع الصلاحيات)
 * - editor: محرر المدونة (صلاحيات المدونة والصفحات فقط)
 * - author: مؤلف (كتابة مقالات + معاينة التسعير)
 * - cashier: كاشير/بائع (عرض الشرائح وطرق البيع والتسعير)
 * - user: مستخدم عام (بدون صلاحيات)
 * - client: عميل (بدون صلاحيات)
 * 
 * لتشغيل هذا الملف:
 * php artisan db:seed --class=RolesSeeder
 */
class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========================================
        // 1️⃣ مسح كاش الصلاحيات من الذاكرة المؤقتة
        // ========================================
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // مسح إضافي لضمان عدم وجود صلاحيات محفوظة قديمة
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // 2️⃣ إنشاء الأدوار (Roles)
        // ========================================
        
        /**
         * المدير العام
         * - له كل الصلاحيات
         * - لون مميز: بنفسجي
         */
        $superAdminRole = Role::firstOrCreate([
            'guard_name' => 'web',
            'name' => 'super_admin'
        ], [
            'display_name' => 'المدير العام',
            'display_name_en' => 'Super Administrator',
            'color' => 'purple'
        ]);

        $adminRole = Role::firstOrCreate([
            'guard_name' => 'web',
            'name' => 'admin'
        ], [
            'display_name' => 'مدير',
            'display_name_en' => 'Administrator'
        ]);

        $editorRole = Role::firstOrCreate([
            'guard_name' => 'web',
            'name' => 'editor'
        ], [
            'display_name' => 'محرر',
            'display_name_en' => 'Editor'
        ]);

        $authorRole = Role::firstOrCreate([
            'guard_name' => 'web',
            'name' => 'author'
        ], [
            'display_name' => 'مؤلف',
            'display_name_en' => 'Author'
        ]);

        $userRole = Role::firstOrCreate([
            'guard_name' => 'web',
            'name' => 'user'
        ], [
            'display_name' => 'مستخدم',
            'display_name_en' => 'User'
        ]);

        $clientRole = Role::firstOrCreate([
            'guard_name' => 'web',
            'name' => 'client'
        ], [
            'display_name' => 'عميل',
            'display_name_en' => 'Client'
        ]);

        $cashierRole = Role::firstOrCreate([
            'guard_name' => 'web',
            'name' => 'cashier'
        ], [
            'display_name' => 'كاشير/بائع',
            'display_name_en' => 'Cashier/Salesman'
        ]);
    
        // ========================================
        // 3️⃣ إنشاء صلاحيات لوحة التحكم
        // ========================================
        $admin_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'admin_view'], ['display_name' => 'عرض  لوحة التحكم', 'display_name_en' => 'View dashboard']);
        $blog_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'blog_view'], ['display_name' => 'عرض المدونة', 'display_name_en' => 'View blog']);
        $blog_add = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'blog_add'], ['display_name' => 'إضافة مقالات', 'display_name_en' => 'Add articles']);
        $blog_update = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'blog_update'], ['display_name' => 'تعديل مقالات', 'display_name_en' => 'Update articles']);
        $blog_publish = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'blog_publish'], ['display_name' => 'نشر مقالات', 'display_name_en' => 'Publish articles']);
        $blog_publish_others = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'blog_publish_others'], ['display_name' => 'نشر مقالات الغير', 'display_name_en' => 'Publishing others\' articles']);
        $blog_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'blog_delete'], ['display_name' => 'حذف مقالات', 'display_name_en' => 'Delete articles']);
        $blog_delete_others = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'blog_delete_others'], ['display_name' => 'حذف مقالات الغير', 'display_name_en' => 'Delete others\' articles']);
        $pages_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pages_view'], ['display_name' => 'عرض الصفحات', 'display_name_en' => 'View pages']);
        $pages_add = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pages_add'], ['display_name' => 'إضافة صفحات', 'display_name_en' => 'Add pages']);
        $pages_update = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pages_update'], ['display_name' => 'تعديل صفحات', 'display_name_en' => 'Update pages']);
        $pages_publish = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pages_publish'], ['display_name' => 'نشر صفحات', 'display_name_en' => 'Publish pages']);
        $pages_publish_others = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pages_publish_others'], ['display_name' => 'نشر صفحات الغير', 'display_name_en' => 'Publishing others\' pages']);
        $pages_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pages_delete'], ['display_name' => 'حذف مقالات', 'display_name_en' => 'Delete pages']);
        $pages_delete_others = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pages_delete_others'], ['display_name' => 'حذف صفحات الغير', 'display_name_en' => 'Delete others\' pages']);
        $cats_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'cats_view'], ['display_name' => 'عرض التصنيفات', 'display_name_en' => 'View categories']);
        $cats_add = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'cats_add'], ['display_name' => 'إضافة تصنيفات', 'display_name_en' => 'Add categories']);
        $cats_update = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'cats_update'], ['display_name' => 'تعديل التصنيفات', 'display_name_en' => 'Update categories']);
        $cats_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'cats_delete'], ['display_name' => 'حذف التصنيفات', 'display_name_en' => 'Delete categories']);
        $roles_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'roles_view'], ['display_name' => 'عرض  الصلاحيات', 'display_name_en' => 'View roles']);
        $roles_add = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'roles_add'], ['display_name' => 'إضافة صلاحيات', 'display_name_en' => 'Add roles']);
        $roles_update = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'roles_update'], ['display_name' => 'تعديل الصلاحيات', 'display_name_en' => 'Update roles']);
        $roles_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'roles_delete'], ['display_name' => 'حذف صلاحيات', 'display_name_en' => 'Delete roles']);
        $users_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'users_view'], ['display_name' => 'عرض  المستخدمين', 'display_name_en' => 'View users']);
        $users_add = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'users_add'], ['display_name' => 'إضافة مستخدمين', 'display_name_en' => 'Add users']);
        $users_update = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'users_update'], ['display_name' => 'تعديل مستخدمين', 'display_name_en' => 'Update users']);
        $users_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'users_delete'], ['display_name' => 'حذف مستخدمين', 'display_name_en' => 'Delete users']);
        $settings_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'settings_view'], ['display_name' => 'عرض الإعدادات', 'display_name_en' => 'View settings']);
        $settings_update = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'settings_update'], ['display_name' => 'تعديل الإعدادات', 'display_name_en' => 'Update settings']);
        $pricing_tiers_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pricing_tiers_view'], ['display_name' => 'عرض الشرائح السعرية', 'display_name_en' => 'View pricing tiers']);
        $pricing_tiers_add = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pricing_tiers_add'], ['display_name' => 'إضافة شرائح سعرية', 'display_name_en' => 'Add pricing tiers']);
        $pricing_tiers_update = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pricing_tiers_update'], ['display_name' => 'تعديل الشرائح السعرية', 'display_name_en' => 'Update pricing tiers']);
        $pricing_tiers_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pricing_tiers_delete'], ['display_name' => 'حذف الشرائح السعرية', 'display_name_en' => 'Delete pricing tiers']);
        $sale_methods_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'sale_methods_view'], ['display_name' => 'عرض طرق البيع', 'display_name_en' => 'View sale methods']);
        $sale_methods_add = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'sale_methods_add'], ['display_name' => 'إضافة طرق بيع', 'display_name_en' => 'Add sale methods']);
        $sale_methods_update = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'sale_methods_update'], ['display_name' => 'تعديل طرق البيع', 'display_name_en' => 'Update sale methods']);
        $sale_methods_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'sale_methods_delete'], ['display_name' => 'حذف طرق البيع', 'display_name_en' => 'Delete sale methods']);
        $pricing_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'pricing_view'], ['display_name' => 'معاينة التسعير', 'display_name_en' => 'View pricing']);
        $warehouses_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'warehouses_view'], ['display_name' => 'عرض المخازن', 'display_name_en' => 'View warehouses']);
        $warehouses_add = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'warehouses_add'], ['display_name' => 'إضافة مخازن', 'display_name_en' => 'Add warehouses']);
        $warehouses_update = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'warehouses_update'], ['display_name' => 'تعديل المخازن', 'display_name_en' => 'Update warehouses']);
        $warehouses_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'warehouses_delete'], ['display_name' => 'حذف المخازن', 'display_name_en' => 'Delete warehouses']);
        $products_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'products_view'], ['display_name' => 'عرض المنتجات', 'display_name_en' => 'View products']);
        $products_create = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'products_create'], ['display_name' => 'إضافة منتجات', 'display_name_en' => 'Create products']);
        $products_edit = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'products_edit'], ['display_name' => 'تعديل منتجات', 'display_name_en' => 'Edit products']);
        $products_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'products_delete'], ['display_name' => 'حذف منتجات', 'display_name_en' => 'Delete products']);
        $inventory_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'inventory_view'], ['display_name' => 'عرض المخزون', 'display_name_en' => 'View inventory']);
        $inventory_add = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'inventory_add'], ['display_name' => 'إضافة مخزون', 'display_name_en' => 'Add inventory']);
        $inventory_adjust = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'inventory_adjust'], ['display_name' => 'تعديل المخزون', 'display_name_en' => 'Adjust inventory']);
        $inventory_transfer = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'inventory_transfer'], ['display_name' => 'نقل المخزون', 'display_name_en' => 'Transfer inventory']);
        $purchase_invoices_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'purchase_invoices_view'], ['display_name' => 'عرض فواتير الشراء', 'display_name_en' => 'View purchase invoices']);
        $purchase_invoices_create = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'purchase_invoices_create'], ['display_name' => 'إنشاء فواتير شراء', 'display_name_en' => 'Create purchase invoices']);
        $purchase_invoices_edit = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'purchase_invoices_edit'], ['display_name' => 'تعديل فواتير الشراء', 'display_name_en' => 'Edit purchase invoices']);
        $purchase_invoices_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'purchase_invoices_delete'], ['display_name' => 'حذف فواتير الشراء', 'display_name_en' => 'Delete purchase invoices']);
        $purchase_invoices_complete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'purchase_invoices_complete'], ['display_name' => 'إتمام فواتير الشراء', 'display_name_en' => 'Complete purchase invoices']);
        $sale_invoices_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'sale_invoices_view'], ['display_name' => 'عرض فواتير البيع', 'display_name_en' => 'View sale invoices']);
        $sale_invoices_create = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'sale_invoices_create'], ['display_name' => 'إنشاء فواتير بيع', 'display_name_en' => 'Create sale invoices']);
        $sale_invoices_edit = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'sale_invoices_edit'], ['display_name' => 'تعديل فواتير البيع', 'display_name_en' => 'Edit sale invoices']);
        $sale_invoices_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'sale_invoices_delete'], ['display_name' => 'حذف فواتير البيع', 'display_name_en' => 'Delete sale invoices']);
        $sale_invoices_complete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'sale_invoices_complete'], ['display_name' => 'إتمام فواتير البيع', 'display_name_en' => 'Complete sale invoices']);
        $sales_cancel = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'sales_cancel'], ['display_name' => 'إلغاء فواتير البيع', 'display_name_en' => 'Cancel sale invoices']);
        $suppliers_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'suppliers_view'], ['display_name' => 'عرض الموردين', 'display_name_en' => 'View suppliers']);
        $suppliers_add = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'suppliers_add'], ['display_name' => 'إضافة موردين', 'display_name_en' => 'Add suppliers']);
        $suppliers_update = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'suppliers_update'], ['display_name' => 'تعديل موردين', 'display_name_en' => 'Update suppliers']);
        $suppliers_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'suppliers_delete'], ['display_name' => 'حذف موردين', 'display_name_en' => 'Delete suppliers']);
        $customers_view = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'customers_view'], ['display_name' => 'عرض العملاء', 'display_name_en' => 'View customers']);
        $customers_add = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'customers_add'], ['display_name' => 'إضافة عملاء', 'display_name_en' => 'Add customers']);
        $customers_update = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'customers_update'], ['display_name' => 'تعديل عملاء', 'display_name_en' => 'Update customers']);
        $customers_delete = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'customers_delete'], ['display_name' => 'حذف عملاء', 'display_name_en' => 'Delete customers']);

        // ========================================
        // 1️⃣4️⃣ مسح الكاش وتزامن الصلاحيات مع الأدوار
        // ========================================
        // مسح كاش الصلاحيات بعد إنشاء جميع الصلاحيات
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // جلب جميع أسماء الصلاحيات
        $permissions = Permission::pluck('name')->toArray();

        // إسناد جميع الصلاحيات للمدير العام
        $superAdminRole->syncPermissions($permissions);
        
        // إسناد الصلاحيات للمدير (مع استثناء التعديل والحذف للفواتير بناءً على الطلب)
        $adminPermissions = array_diff($permissions, [
            'sale_invoices_edit', 
            'sale_invoices_delete', 
            'sales_cancel',
            'purchase_invoices_edit',
            'purchase_invoices_delete'
        ]);
        $adminRole->syncPermissions($adminPermissions);

        /**
         * صلاحيات محرر المدونة
         * - صلاحيات المدونة الكاملة
         * - صلاحيات الصفحات الكاملة
         * - صلاحيات التصنيفات الكاملة
         */
        $editorRole->syncPermissions([
            'admin_view',
            'blog_view',
            'blog_add',
            'blog_publish',
            'blog_update',
            'blog_publish_others',
            'blog_delete',
            'blog_delete_others',
            'pages_view',
            'pages_add',
            'pages_publish',
            'pages_publish_others',
            'pages_delete',
            'pages_delete_others',
            'cats_view',
            'cats_add',
            'cats_update',
            'cats_delete'
        ]);

        /**
         * صلاحيات المؤلف
         * - يمكنه إضافة مقالات وصفحات
         * - يمكنه إضافة تصنيفات
         * - يمكنه معاينة التسعير
         */
        $authorRole->syncPermissions([
            'admin_view',
            'blog_view',
            'blog_add',
            'pages_view',
            'pages_add',
            'cats_view',
            'cats_add',
            'pricing_view'
        ]);

        /**
         * صلاحيات الكاشير/البائع
         * - يمكنه الوصول لوحة التحكم
         * - يمكنه عرض الشرائح السعرية
         * - يمكنه عرض طرق البيع
         * - يمكنه معاينة التسعير
         * - يمكنه عرض المخازن
         */
        $cashierRole->syncPermissions([
            'admin_view',
            'pricing_tiers_view',
            'sale_methods_view',
            'pricing_view',
            'warehouses_view',
            'products_view',
            'sale_invoices_view',
            'sale_invoices_create'
        ]);

        // ========================================
        // 1️⃣5️⃣ إنشاء مستخدمين افتراضيين للاختبار
        // ========================================

        /**
         * السوبر أدمن: محمود حسن
         */
        $superAdmin = User::updateOrCreate([
            'email'              => 'info@bzzix.com'
        ], [
            'name'               => 'محمود حسن',
            'email_verified_at'  => Carbon::now(),
            'password'           => Hash::make('password')
        ]);
        $superAdmin->assignRole('super_admin');
    
        /**
         * بائع: ادم
         */
        $adam = User::updateOrCreate([
            'email'              => 'adam@bzzix.com'
        ], [
            'name'               => 'ادم',
            'email_verified_at'  => Carbon::now(),
            'password'           => Hash::make('password')
        ]);
        $adam->assignRole('cashier');
    
        /**
         * بائع: فاطمة عوض
         */
        $fatema = User::updateOrCreate([
            'email'              => 'fatema@bzzix.com'
        ], [
            'name'               => 'فاطمة عوض',
            'email_verified_at'  => Carbon::now(),
            'password'           => Hash::make('password')
        ]);
        $fatema->assignRole('cashier');

    
    }
}
