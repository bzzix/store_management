<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        $this->logActivity($customer, 'created', "تم إضافة عميل جديد: {$customer->name}");
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        $changes = $customer->getChanges();
        unset($changes['updated_at']);
        
        if (empty($changes)) return;

        $this->logActivity($customer, 'updated', "تم تحديث بيانات العميل: {$customer->name}", $changes);
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        $this->logActivity($customer, 'deleted', "تم حذف العميل: {$customer->name}");
    }

    /**
     * Helper to log activity
     */
    protected function logActivity($model, $type, $description, $properties = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => $type,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
