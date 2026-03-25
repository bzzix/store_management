<?php

namespace App\Livewire\Dashboard\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class GeneralSettings extends Component
{
    use WithFileUploads;

    // General Info
    public $appName;
    public $appManagerName;
    public $appDisc;
    public $appMail;
    public $appMobile;
    public $appPhone;
    public $appAddress;

    // Branding
    public $appLogo;
    public $appMiniLogo;
    public $appDarkLogo;
    public $appMiniDarkLogo;
    public $appIcon;
    
    // Uploaded files
    public $newLogo;
    public $newMiniLogo;
    public $newDarkLogo;
    public $newMiniDarkLogo;
    public $newIcon;

    // System
    public $appInvoiceGenerate;
    public $AppGetInstallments;
    public $appHomepage;
    public $appNewAccount;
    public $appDefaultRole;

    // Content
    public $appPolicy;
    public $appTerms;

    public function mount()
    {
        $this->appName = get_setting('appName');
        $this->appManagerName = get_setting('appManagerName');
        $this->appDisc = get_setting('appDisc');
        $this->appMail = get_setting('appMail');
        $this->appMobile = get_setting('appMobile');
        $this->appPhone = get_setting('appPhone');
        $this->appAddress = get_setting('appAddress');

        $this->appLogo = get_setting('appLogo');
        $this->appMiniLogo = get_setting('appMiniLogo');
        $this->appDarkLogo = get_setting('appDarkLogo');
        $this->appMiniDarkLogo = get_setting('appMiniDarkLogo');
        $this->appIcon = get_setting('appIcon');

        $this->appInvoiceGenerate = get_setting('appInvoiceGenerate');
        $this->AppGetInstallments = get_setting('AppGetInstallments');
        $this->appHomepage = get_setting('appHomepage');
        $this->appNewAccount = get_setting('appNewAccount');
        $this->appDefaultRole = get_setting('appDefaultRole');

        $this->appPolicy = get_setting('appPolicy');
        $this->appTerms = get_setting('appTerms');
    }

    public function save()
    {
        $this->validate([
            'appName' => 'required|string|max:255',
            'appMail' => 'nullable|email',
            'newLogo' => 'nullable|image|max:1024',
            'newMiniLogo' => 'nullable|image|max:1024',
            'newDarkLogo' => 'nullable|image|max:1024',
            'newMiniDarkLogo' => 'nullable|image|max:1024',
            'newIcon' => 'nullable|image|max:1024',
        ]);

        // Save General
        set_setting('appName', $this->appName);
        set_setting('appManagerName', $this->appManagerName);
        set_setting('appDisc', $this->appDisc);
        set_setting('appMail', $this->appMail);
        set_setting('appMobile', $this->appMobile);
        set_setting('appPhone', $this->appPhone);
        set_setting('appAddress', $this->appAddress);

        // Save System
        set_setting('appInvoiceGenerate', $this->appInvoiceGenerate);
        set_setting('AppGetInstallments', $this->AppGetInstallments);
        set_setting('appHomepage', $this->appHomepage);
        set_setting('appNewAccount', $this->appNewAccount);
        set_setting('appDefaultRole', $this->appDefaultRole);

        // Save Content
        set_setting('appPolicy', $this->appPolicy);
        set_setting('appTerms', $this->appTerms);

        // Handle File Uploads
        if ($this->newLogo) {
            $path = $this->newLogo->store('settings', 'public');
            set_setting('appLogo', Storage::url($path));
            $this->appLogo = get_setting('appLogo');
        }
        if ($this->newMiniLogo) {
            $path = $this->newMiniLogo->store('settings', 'public');
            set_setting('appMiniLogo', Storage::url($path));
            $this->appMiniLogo = get_setting('appMiniLogo');
        }
        if ($this->newDarkLogo) {
            $path = $this->newDarkLogo->store('settings', 'public');
            set_setting('appDarkLogo', Storage::url($path));
            $this->appDarkLogo = get_setting('appDarkLogo');
        }
        if ($this->newMiniDarkLogo) {
            $path = $this->newMiniDarkLogo->store('settings', 'public');
            set_setting('appMiniDarkLogo', Storage::url($path));
            $this->appMiniDarkLogo = get_setting('appMiniDarkLogo');
        }
        if ($this->newIcon) {
            $path = $this->newIcon->store('settings', 'public');
            set_setting('appIcon', Storage::url($path));
            $this->appIcon = get_setting('appIcon');
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Settings updated successfully'),
            'title' => __('Success')
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.settings.general-settings')
            ->layout('dashboard.layouts.master');
    }
}
