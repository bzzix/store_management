<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

class SettingsForm extends Component
{
    use WithFileUploads;

    // Basic Info
    public $appName;
    public $appManagerName;
    public $appAddress;
    
    // Contact Info
    public $appMobile;
    public $appPhone;
    public $appMail;
    
    // Branding
    public $appLogo;
    public $appIcon;
    
    // Temporary Uploads
    public $newLogo;
    public $newIcon;

    // Financial & System
    public $appCurrency;
    public $appTaxNumber;
    public $appInvoicePrefix;

    public function mount()
    {
        $this->appName = get_setting('appName', 'أولاد عبد الستار للزراعة');
        $this->appManagerName = get_setting('appManagerName', 'محمود حسن');
        $this->appAddress = get_setting('appAddress', 'كفر الشيخ - الرياض');
        
        $this->appMobile = get_setting('appMobile', '01062226955');
        $this->appPhone = get_setting('appPhone', '0473896884');
        $this->appMail = get_setting('appMail', 'info@abdelstar.com');
        
        $this->appLogo = get_setting('appLogo');
        $this->appIcon = get_setting('appIcon');
        
        $this->appCurrency = get_setting('appCurrency', 'EGP');
        $this->appTaxNumber = get_setting('appTaxNumber');
        $this->appInvoicePrefix = get_setting('appInvoicePrefix', 'SAL');
    }

    public function save()
    {
        try {
            $this->validate([
                'appName' => 'required|string|max:255',
                'appManagerName' => 'nullable|string|max:255',
                'appAddress' => 'nullable|string|max:255',
                'appMobile' => 'nullable|string|max:20',
                'appPhone' => 'nullable|string|max:20',
                'appMail' => 'nullable|email|max:255',
                'appCurrency' => 'nullable|string|max:10',
                'appTaxNumber' => 'nullable|string|max:50',
                'appInvoicePrefix' => 'nullable|string|max:10',
                'newLogo' => 'nullable|image|max:10240', // 10MB
                'newIcon' => 'nullable|image|max:20480', // 20MB (Increased to ensure it works)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'msg' => __('يرجى التأكد من البيانات ووجود اللون الأحمر تحت الحقول'),
                'title' => __('خطأ في التحقق')
            ]);
            throw $e;
        }

        \Illuminate\Support\Facades\Log::info("Saving settings for: " . $this->appName);
        set_setting('appName', $this->appName);
        set_setting('appManagerName', $this->appManagerName);
        set_setting('appAddress', $this->appAddress);
        set_setting('appMobile', $this->appMobile);
        set_setting('appPhone', $this->appPhone);
        set_setting('appMail', $this->appMail);
        set_setting('appCurrency', $this->appCurrency);
        set_setting('appTaxNumber', $this->appTaxNumber);
        set_setting('appInvoicePrefix', $this->appInvoicePrefix);

        // Handle Branding Uploads
        if ($this->newLogo) {
            error_log("New logo detected, storing...");
            $path = $this->newLogo->store('branding', 'public');
            error_log("Logo stored at: " . $path);
            set_setting('appLogo', Storage::url($path));
            $this->appLogo = get_setting('appLogo');
            $this->newLogo = null;
        } else {
            error_log("No new logo provided");
        }

        if ($this->newIcon) {
            error_log("New icon detected, storing...");
            $path = $this->newIcon->store('branding', 'public');
            error_log("Icon stored at: " . $path);
            set_setting('appIcon', Storage::url($path));
            $this->appIcon = get_setting('appIcon');
            $this->newIcon = null;
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'msg' => __('Settings saved successfully'), // Changed 'message' to 'msg' to match JS
            'title' => __('Success')
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.settings-form');
    }
}
