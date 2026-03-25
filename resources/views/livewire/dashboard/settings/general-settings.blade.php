<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('General Settings') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="row g-0">
                    <div class="col-12 col-md-3 border-end">
                        <div class="card-body">
                            <h4 class="subheader">{{ __('Settings Menu') }}</h4>
                            <div class="list-group list-group-transparent">
                                <a href="#tab-general" class="list-group-item list-group-item-action d-flex align-items-center active" data-bs-toggle="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37a1.724 1.724 0 0 0 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                                    {{ __('General Info') }}
                                </a>
                                <a href="#tab-branding" class="list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 8l2 -1l2 1" /><path d="M4 12l2 -1l2 1" /><path d="M4 16l2 -1l2 1" /><path d="M8 4l1 2l1 -2" /><path d="M12 4l1 2l1 -2" /><path d="M16 4l1 2l1 -2" /><path d="M20 8l-2 -1l-2 1" /><path d="M20 12l-2 -1l-2 1" /><path d="M20 16l-2 -1l-2 1" /><path d="M8 20l1 -2l1 2" /><path d="M12 20l1 -2l1 2" /><path d="M16 20l1 -2l1 2" /><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /></svg>
                                    {{ __('Branding') }}
                                </a>
                                <a href="#tab-system" class="list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                                    {{ __('System Settings') }}
                                </a>
                                <a href="#tab-legal" class="list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>
                                    {{ __('Legal & Content') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-9 d-flex flex-column">
                        <form wire:submit.prevent="save">
                            <div class="card-body">
                                <div class="tab-content">
                                    <!-- Tab: General -->
                                    <div class="tab-pane active show" id="tab-general">
                                        <h3 class="card-title mb-4">{{ __('General Information') }}</h3>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label required">{{ __('Application Name') }}</label>
                                                <input type="text" wire:model="appName" class="form-control @error('appName') is-invalid @enderror">
                                                @error('appName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Manager Name') }}</label>
                                                <input type="text" wire:model="appManagerName" class="form-control">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">{{ __('Description') }}</label>
                                                <textarea wire:model="appDisc" class="form-control" rows="2"></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Email Address') }}</label>
                                                <input type="email" wire:model="appMail" class="form-control @error('appMail') is-invalid @enderror">
                                                @error('appMail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Mobile') }}</label>
                                                <input type="text" wire:model="appMobile" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Phone') }}</label>
                                                <input type="text" wire:model="appPhone" class="form-control">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">{{ __('Address') }}</label>
                                                <input type="text" wire:model="appAddress" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab: Branding -->
                                    <div class="tab-pane" id="tab-branding">
                                        <h3 class="card-title mb-4">{{ __('Branding & Logos') }}</h3>
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Main Logo') }}</label>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if($newLogo)
                                                        <img src="{{ $newLogo->temporaryUrl() }}" class="img-thumbnail" style="height: 80px;">
                                                    @elseif($appLogo)
                                                        <img src="{{ $appLogo }}" class="img-thumbnail" style="height: 80px;">
                                                    @endif
                                                    <input type="file" wire:model="newLogo" class="form-control">
                                                </div>
                                                @error('newLogo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Mini Logo') }}</label>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if($newMiniLogo)
                                                        <img src="{{ $newMiniLogo->temporaryUrl() }}" class="img-thumbnail" style="height: 50px;">
                                                    @elseif($appMiniLogo)
                                                        <img src="{{ $appMiniLogo }}" class="img-thumbnail" style="height: 50px;">
                                                    @endif
                                                    <input type="file" wire:model="newMiniLogo" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Dark Mode Logo') }}</label>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if($newDarkLogo)
                                                        <img src="{{ $newDarkLogo->temporaryUrl() }}" class="img-thumbnail bg-dark" style="height: 80px;">
                                                    @elseif($appDarkLogo)
                                                        <img src="{{ $appDarkLogo }}" class="img-thumbnail bg-dark" style="height: 80px;">
                                                    @endif
                                                    <input type="file" wire:model="newDarkLogo" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('App Icon / Favicon') }}</label>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if($newIcon)
                                                        <img src="{{ $newIcon->temporaryUrl() }}" class="img-thumbnail" style="height: 48px;">
                                                    @elseif($appIcon)
                                                        <img src="{{ $appIcon }}" class="img-thumbnail" style="height: 48px;">
                                                    @endif
                                                    <input type="file" wire:model="newIcon" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab: System -->
                                    <div class="tab-pane" id="tab-system">
                                        <h3 class="card-title mb-4">{{ __('System Configuration') }}</h3>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-label">{{ __('Auto Generate Invoice Number') }}</div>
                                                <label class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" wire:model="appInvoiceGenerate">
                                                    <span class="form-check-label">{{ __('Enabled') }}</span>
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Installments Frequency') }}</label>
                                                <select wire:model="AppGetInstallments" class="form-select">
                                                    <option value="everyMonth">الشهرية</option>
                                                    <option value="everyYear">السنوية</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Default User Role') }}</label>
                                                <input type="text" wire:model="appDefaultRole" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-label">{{ __('Allow New Registration') }}</div>
                                                <label class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" wire:model="appNewAccount">
                                                    <span class="form-check-label">{{ __('Allow') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab: Legal -->
                                    <div class="tab-pane" id="tab-legal">
                                        <h3 class="card-title mb-4">{{ __('Policies & Terms') }}</h3>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">{{ __('Privacy Policy') }}</label>
                                                <textarea wire:model="appPolicy" class="form-control" rows="10"></textarea>
                                            </div>
                                            <div class="col-12 mt-4">
                                                <label class="form-label">{{ __('Terms of Use') }}</label>
                                                <textarea wire:model="appTerms" class="form-control" rows="10"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent mt-auto">
                                <div class="btn-list justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-4 0l0 -4" /></svg>
                                        {{ __('Save Settings') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
