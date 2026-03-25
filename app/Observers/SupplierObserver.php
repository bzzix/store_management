<?php

namespace App\Observers;

use App\Models\Supplier;

class SupplierObserver
{
    public function created(Supplier $supplier): void
    {
        session()->flash('success', __('Supplier added: ') . $supplier->name);
    }

    public function updated(Supplier $supplier): void
    {
        session()->flash('success', __('Supplier info updated: ') . $supplier->name);
    }

    public function deleted(Supplier $supplier): void
    {
        session()->flash('info', __('Supplier removed: ') . $supplier->name);
    }
}
