<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null) {
        $tenantId =  'central';
        $cacheKey = "{$tenantId}_setting_{$key}";

        return Cache::store('file')->rememberForever($cacheKey, function () use ($key, $default) {
            return Setting::where('key', $key)->value('value') ?? $default;
        });
    }
}

if (!function_exists('set_setting')) {
    function set_setting($key, $value) {
        $tenantId =  'central';
        $cacheKey = "{$tenantId}_setting_{$key}";

        Cache::store('file')->forget($cacheKey);
        return Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}

if (!function_exists('delete_setting')) {
    function delete_setting($key) {
        $tenantId =  'central';
        $cacheKey = "{$tenantId}_setting_{$key}";

        // حذف من الكاش
        Cache::store('file')->forget($cacheKey);
        
        // حذف من قاعدة البيانات
        return Setting::where('key', $key)->delete();
    }
}
