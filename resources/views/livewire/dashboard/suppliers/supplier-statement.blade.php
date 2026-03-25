<div class="space-y-8 max-w-5xl mx-auto font-cairo">
    {{-- Header with Print Button --}}
    <div class="flex justify-between items-center no-print">
        <div class="flex items-center gap-4">
            <div class="p-3 rounded-2xl bg-primary-50 text-primary-600 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                    <path d="M9 17h6" />
                    <path d="M9 13h6" />
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-surface-900 leading-tight">{{ __('Supplier Statement') }}</h2>
                <p class="text-surface-500 font-medium">سجل الحركات المالية التفصيلي للمورد</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('dashboard.suppliers.index') }}" class="px-6 py-2.5 rounded-2xl bg-white border border-surface-200 text-surface-700 font-bold hover:bg-surface-50 transition-all shadow-sm">
                {{ __('Back') }}
            </a>
            <a href="{{ route('dashboard.suppliers.statement.print', ['supplier' => $supplier->id, 'fromDate' => $fromDate, 'toDate' => $toDate]) }}" 
                target="_blank"
                class="px-6 py-2.5 rounded-2xl bg-primary-600 text-white font-bold hover:bg-primary-700 transition-all shadow-lg shadow-primary-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                {{ __('Print (80mm)') }}
            </a>
        </div>
    </div>

    {{-- Main Statement Document --}}
    <div class="bg-white border-2 border-surface-200 rounded-[2rem] overflow-hidden shadow-xl print:shadow-none print:border-none">
        {{-- Formal Header --}}
        <div class="p-8 border-b border-surface-100 bg-surface-50/30">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-center md:text-right">
                    @if(get_setting('appLogo'))
                        <img src="{{ get_setting('appLogo') }}" class="h-24 mx-auto md:mr-0 mb-2 object-contain">
                    @else
                        <h1 class="text-3xl font-bold text-surface-900">{{ get_setting('appName', __('ABDELSTAR AGRI')) }}</h1>
                    @endif
                    <p class="text-surface-600 font-bold mt-1 text-lg">{{ __('Management') }} / {{ get_setting('appManagerName', 'محمود حسن') }}</p>
                </div>
                
                <div class="text-center">
                    <div class="inline-block px-6 py-2 rounded-2xl bg-primary-600 text-white font-bold text-xl mb-2">
                        {{ __('Supplier Statement') }}
                    </div>
                    <p class="text-surface-500 font-outfit">{{ Carbon\Carbon::now()->format('Y/m/d H:i') }}</p>
                </div>

                <div class="text-center md:text-left space-y-1 text-sm font-medium text-surface-600">
                    <p>{{ get_setting('appAddress', 'كفر الشيخ - الرياض') }}</p>
                    <p class="font-outfit font-bold text-surface-900">{{ get_setting('appMobile', '01062226955') }}</p>
                    <p class="font-outfit font-bold text-surface-900">{{ get_setting('appPhone', '0473896884') }}</p>
                </div>
            </div>
        </div>

        {{-- Info Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-surface-100 border-b border-surface-100">
            {{-- Supplier Info --}}
            <div class="p-8 space-y-4">
                <h3 class="text-sm font-bold text-primary-600 uppercase tracking-wider">{{ __('Supplier Information') }}</h3>
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="text-surface-400 text-sm">{{ __('Name') }}:</span>
                        <span class="text-surface-900 font-bold">{{ $supplier->name }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-surface-400 text-sm">{{ __('Phone') }}:</span>
                        <span class="text-surface-900 font-bold font-outfit">{{ $supplier->phone }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-surface-400 text-sm">{{ __('Opening Balance') }}:</span>
                        <span class="text-surface-900 font-bold font-outfit">{{ number_format($supplier->opening_balance, 2) }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-surface-400 text-sm">{{ __('Current Balance') }}:</span>
                        <span class="font-bold font-outfit {{ $supplier->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($supplier->current_balance, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Statement Period --}}
            <div class="p-8 space-y-6">
                <h3 class="text-sm font-bold text-primary-600 uppercase tracking-wider">{{ __('Filtering & Period') }}</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-surface-500">{{ __('From Date') }}</label>
                        <input type="date" wire:model.live="fromDate" class="w-full h-10 px-3 rounded-xl border-surface-200 bg-surface-50/50 focus:border-primary-500 focus:ring-0 text-sm font-outfit transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-surface-500">{{ __('To Date') }}</label>
                        <input type="date" wire:model.live="toDate" class="w-full h-10 px-3 rounded-xl border-surface-200 bg-surface-50/50 focus:border-primary-500 focus:ring-0 text-sm font-outfit transition-all">
                    </div>
                </div>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-surface-50/50 border-b-2 border-surface-100 uppercase tracking-wider text-xs font-bold text-surface-600">
                        <th class="px-4 py-4 text-center w-12">#</th>
                        <th class="px-4 py-4 text-center w-40">{{ __('Ref / Date') }}</th>
                        <th class="px-4 py-4 text-center">{{ __('Previous Balance') }}</th>
                        <th class="px-4 py-4 text-center">{{ __('Transaction Value') }}</th>
                        <th class="px-4 py-4 text-center text-green-600">{{ __('Paid / Payment') }}</th>
                        <th class="px-4 py-4 text-center bg-surface-100/30">{{ __('Running Balance') }}</th>
                        <th class="px-4 py-4 text-center no-print w-24">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    {{-- Previous Balance Row --}}
                    <tr class="bg-primary-50/10 font-medium">
                        <td class="px-4 py-3 text-sm text-surface-400 text-center font-outfit">-</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs font-bold text-primary-600 block leading-none mb-1">{{ __('Opening Balance') }}</span>
                            <span class="text-[10px] text-surface-400 font-outfit">{{ $fromDate }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-surface-400 text-center">-</td>
                        <td class="px-4 py-3 text-sm text-surface-400 text-center">-</td>
                        <td class="px-4 py-3 text-sm text-surface-400 text-center">-</td>
                        <td class="px-4 py-3 text-sm font-black text-surface-900 font-outfit text-center bg-primary-50/30">
                            {{ number_format($transactions['previous_balance'], 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-surface-400 text-center no-print">-</td>
                    </tr>

                    @if (count($items) > 0)
                        @foreach ($items as $index => $item)
                            <tr class="hover:bg-surface-50 transition-all group">
                                <td class="px-4 py-4 text-sm text-surface-400 text-center font-outfit">{{ $index + 1 }}</td>
                                <td class="px-4 py-4 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-black text-surface-900 font-outfit bg-surface-100 px-2 py-0.5 rounded-lg border border-surface-200/50 mb-1 leading-none">
                                            {{ $item['number'] }}
                                        </span>
                                        <div class="flex items-center gap-1.5 text-[10px] text-surface-400 font-outfit">
                                            <span>{{ \Carbon\Carbon::parse($item['date'])->format('Y-m-d') }}</span>
                                            <span class="opacity-50">|</span>
                                            <span>{{ \Carbon\Carbon::parse($item['date'])->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-surface-500 font-outfit text-center">
                                    {{ number_format($item['previous_balance'], 2) }}
                                </td>
                                <td class="px-4 py-4 text-sm text-surface-900 font-outfit text-center font-medium">
                                    {{ $item['type'] === 'purchase' ? number_format($item['value'], 2) : '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm font-bold @if($item['deduction'] > 0) text-green-600 bg-green-50/30 @else text-surface-300 @endif font-outfit text-center">
                                    {{ $item['deduction'] > 0 ? number_format($item['deduction'], 2) : '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm font-black text-surface-900 font-outfit text-center bg-surface-100/20">
                                    {{ number_format($item['balance'], 2) }}
                                </td>
                                <td class="px-4 py-4 text-sm text-center no-print">
                                    @if($item['url'] != '#')
                                        <a href="{{ $item['url'] }}" target="_blank" 
                                            class="inline-flex items-center justify-center p-2 rounded-xl bg-surface-100/50 text-surface-600 hover:bg-primary-600 hover:text-white transition-all shadow-sm group-hover:shadow-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M12 18c-4 0 -7 -3 -7 -6c0 -3 3 -6 7 -6c4 0 7 3 7 6" /></svg>
                                        </a>
                                    @else
                                        <span class="text-surface-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Totals Grid --}}
        <div class="flex justify-end p-8 border-t-2 border-surface-100 bg-surface-50/30">
            <div class="w-full md:w-96 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col p-4 rounded-3xl bg-white border border-surface-100 shadow-sm">
                        <span class="text-xs font-bold text-surface-500 mb-1">{{ __('Previous Balance') }}</span>
                        <span class="text-xl font-bold text-surface-900 font-outfit">{{ number_format($transactions['previous_balance'], 2) }}</span>
                    </div>
                    <div class="flex flex-col p-4 rounded-3xl bg-white border border-surface-100 shadow-sm">
                        <span class="text-xs font-bold text-primary-600 mb-1">عدد الحركات</span>
                        <span class="text-xl font-bold text-surface-900 font-outfit">{{ count($items) }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center py-3 px-5 rounded-2xl bg-white border border-surface-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-red-50 text-red-600 border border-red-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        </div>
                        <span class="text-sm font-bold text-surface-700">{{ __('Total Addition') }}</span>
                    </div>
                    <span class="text-xl font-bold text-red-600 font-outfit">{{ number_format($transactions['total_addition'], 2) }}</span>
                </div>

                <div class="flex justify-between items-center py-3 px-5 rounded-2xl bg-white border border-surface-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-green-50 text-green-600 border border-green-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /></svg>
                        </div>
                        <span class="text-sm font-bold text-surface-700">{{ __('Total Deduction') }}</span>
                    </div>
                    <span class="text-xl font-bold text-green-600 font-outfit">{{ number_format($transactions['total_deduction'], 2) }}</span>
                </div>

                <div class="flex justify-between items-center p-6 rounded-[2rem] bg-surface-900 text-white shadow-2xl shadow-surface-200">
                    <span class="text-lg font-bold">{{ __('Final Balance') }}</span>
                    <div class="text-right">
                        <span class="text-3xl font-bold font-outfit block">{{ number_format($transactions['final_balance'], 2) }}</span>
                        <span class="text-[10px] opacity-70">{{ $transactions['final_balance'] > 0 ? 'رصيد مستحق للمورد' : 'رصيد دائن للشركة' }}</span>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading class="fixed inset-0 bg-surface-900/10 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-3xl shadow-2xl flex flex-col items-center gap-4">
            <svg class="animate-spin w-10 h-10 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="text-surface-600 font-bold font-cairo text-sm">{{ __('Processing...') }}</span>
        </div>
    </div>
</div>
