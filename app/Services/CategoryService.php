<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\QueryException;

class CategoryService
{
    /**
     * إنشاء تصنيف جديد
     */
    public function createCategory(array $data): Category
    {
        try {
            // توليد slug من الاسم
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
            }

            return Category::create($data);
        } catch (QueryException $e) {
            throw new \Exception(__('Error creating category: ') . $e->getMessage());
        }
    }

    /**
     * تحديث تصنيف
     */
    public function updateCategory(Category $category, array $data): Category
    {
        try {
            // توليد slug من الاسم إذا تغير الاسم
            if (isset($data['name']) && $data['name'] !== $category->name) {
                if (!isset($data['slug']) || empty($data['slug'])) {
                    $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
                }
            }

            $category->update($data);
            return $category;
        } catch (QueryException $e) {
            throw new \Exception(__('Error updating category: ') . $e->getMessage());
        }
    }

    /**
     * حذف تصنيف
     */
    public function deleteCategory(Category $category): bool
    {
        try {
            // التحقق من وجود منتجات في التصنيف
            if ($category->products()->exists()) {
                throw new \Exception(__('Cannot delete category with products. Please delete products first or change their category.'));
            }

            return $category->delete();
        } catch (QueryException $e) {
            throw new \Exception(__('Error deleting category: ') . $e->getMessage());
        }
    }

    /**
     * الحصول على جميع التصنيفات النشطة
     */
    public function getActiveCategories()
    {
        return Category::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    /**
     * التحقق من وجود تصنيف بنفس الاسم
     */
    public function isNameExists(string $name, ?int $excludeId = null): bool
    {
        $query = Category::where('name', $name);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
