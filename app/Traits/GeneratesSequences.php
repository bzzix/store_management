<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneratesSequences
{
    /**
     * Generate a unique temporary number for a model.
     * Use this in the 'creating' event to avoid race conditions.
     * 
     * @param Model $model
     * @param string $prefix
     * @param string $columnName
     * @return void
     */
    protected function setSequentialNumber(Model $model, string $prefix, string $columnName = 'invoice_number')
    {
        if (empty($model->$columnName)) {
            // Use a temporary unique placeholder that won't conflict even under heavy concurrency
            // Format: TMP-PREFIX-TIMESTAMP-RAND
            $model->$columnName = 'TMP-' . $prefix . '-' . now()->format('Hisv') . '-' . Str::random(4);
        }
    }

    /**
     * Update the number with the actual ID after creation for absolute uniqueness.
     * Format: PREFIX-YYYYMMDD-ID (padded)
     * 
     * @param Model $model
     * @param string $prefix
     * @param string $columnName
     * @param int $padding
     */
    protected function updateNumberWithId(Model $model, string $prefix, string $columnName = 'invoice_number', int $padding = 4)
    {
        $date = $model->created_at ? $model->created_at->format('Ymd') : date('Ymd');
        $number = $prefix . '-' . $date . '-' . str_pad($model->id, $padding, '0', STR_PAD_LEFT);
        
        // Update without triggering events again
        $model->updateQuietly([$columnName => $number]);
    }
}
