<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    public function created(Category $category): void
    {
        session()->flash('success', __('Category created: ') . $category->name);
    }

    public function updated(Category $category): void
    {
        session()->flash('success', __('Category updated: ') . $category->name);
    }

    public function deleted(Category $category): void
    {
        session()->flash('info', __('Category deleted: ') . $category->name);
    }
}
