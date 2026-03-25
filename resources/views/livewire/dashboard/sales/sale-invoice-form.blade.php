<div x-data="{ show: @entangle('showModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-surface-900/50 backdrop-blur-sm" @click="show = false"></div>

        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
            class="inline-block w-full max-w-6xl overflow-hidden text-right align-bottom transition-all transform bg-white shadow-2xl rounded-2xl sm:my-8 sm:align-middle">
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-surface-100 bg-surface-50/50 flex items-center justify-between">
                <h3 class="text-xl font-bold text-surface-900">
                    {{ $isReadOnly ? __('Invoice Details') : ($isEdit ? __('Edit Sale Invoice') : __('New Sale Invoice')) }}
                </h3>
                <button @click="show = false" class="p-2 text-surface-400 hover:text-surface-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    {{-- Customer --}}
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-semibold text-surface-700">{{ __('Customer') }}</label>
                        <select wire:model.live="customer_id" @if($isReadOnly) disabled @endif class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none disabled:bg-surface-50">
                            <option value="">{{ __('Select Customer') }}</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                        @error('customer_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror

                        @if($customer_id)
                            <div class="mt-2 text-sm font-black flex justify-between items-center p-3 rounded-xl shadow-inner {{ $customer_balance > 0 ? 'bg-red-50 text-red-700 border border-red-100' : ($customer_balance < 0 ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-surface-50 text-surface-600 border border-border-surface-100') }}">
                                <span>{{ __('Previous Balance') }}:</span>
                                <span>{{ number_format(abs($customer_balance), 2) }} {{ $customer_balance > 0 ? __('Debt') : ($customer_balance < 0 ? __('Credit') : '') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Warehouse --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-surface-700">{{ __('Warehouse') }}</label>
                        <select wire:model.live="warehouse_id" @if($isReadOnly) disabled @endif class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none disabled:bg-surface-50">
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sale Method --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-surface-700">{{ __('Sale Method') }}</label>
                        <select wire:model.live="sale_method_code" @if($isReadOnly) disabled @endif class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none disabled:bg-surface-50">
                            @foreach($saleMethods as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Car & Driver info --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-surface-700">{{ __('Car Number') }}</label>
                        <input type="text" wire:model="car_number" @if($isReadOnly) disabled @endif placeholder="{{ __('Car No.') }}" class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none disabled:bg-surface-50">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-surface-700">{{ __('Driver Name') }}</label>
                        <input type="text" wire:model="driver_name" @if($isReadOnly) disabled @endif placeholder="{{ __('Driver Name') }}" class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none disabled:bg-surface-50">
                    </div>
                </div>

                @if(!$isReadOnly)
                    {{-- Product Search --}}
                    <div class="relative mb-6">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <svg wire:loading.remove wire:target="searchProduct" class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <svg wire:loading wire:target="searchProduct" class="animate-spin h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="searchProduct" placeholder="{{ __('Search products by name, SKU or barcode...') }}" 
                            class="w-full pr-12 pl-4 py-3 rounded-2xl border-2 border-primary-100 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none text-lg">
                        
                        @if(!empty($searchResults))
                            <div class="absolute z-50 w-full mt-2 bg-white border border-surface-100 rounded-2xl shadow-xl max-h-80 overflow-y-auto">
                                @foreach($searchResults as $product)
                                    <button type="button" wire:click="addProduct({{ $product->id }})" class="w-full px-5 py-3 text-right hover:bg-primary-50 flex items-center justify-between border-b border-surface-50 last:border-0 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-surface-100 flex items-center justify-center font-bold text-surface-500 italic">
                                                @if($product->main_image)
                                                    <img src="{{ Storage::url($product->main_image) }}" class="w-full h-full object-cover rounded-xl">
                                                @else
                                                    {{ substr($product->name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-surface-900">{{ $product->name }}</div>
                                                <div class="text-xs text-surface-500">SKU: {{ $product->sku }} | Barcode: {{ $product->barcode }}</div>
                                            </div>
                                        </div>
                                        <div class="text-left">
                                            <div class="text-primary-600 font-bold">{{ number_format($product->current_base_price, 0) }} {{ __('EGP') }}</div>
                                            @php
                                                $availableStock = $product->warehouseStock->sum('quantity');
                                            @endphp
                                            <div class="text-xs {{ $availableStock > 0 ? 'text-green-500' : 'text-red-500' }} font-bold">
                                                {{ __('Stock') }}: {{ number_format($availableStock, 0) }} {{ $product->base_unit }}
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @error('items') <div class="mt-2 text-sm text-red-500 font-bold">{{ $message }}</div> @enderror
                        @error('items.*') <div class="mt-2 text-sm text-red-500 font-bold">{{ $message }}</div> @enderror
                    </div>
                @endif

                {{-- Items Table --}}
                <div class="overflow-x-auto border border-surface-100 rounded-2xl mb-8">
                    <table class="w-full text-sm text-right">
                        <thead class="bg-surface-50/50 text-surface-600 font-bold">
                            <tr>
                                <th class="px-4 py-3 border-b border-surface-100">#</th>
                                <th class="px-4 py-3 border-b border-surface-100">{{ __('Product') }}</th>
                                <th class="px-4 py-3 border-b border-surface-100 w-32">{{ __('Unit') }}</th>
                                <th class="px-4 py-3 border-b border-surface-100 w-32">{{ __('Quantity') }}</th>
                                <th class="px-4 py-3 border-b border-surface-100 w-32">{{ __('Price') }}</th>
                                <th class="px-4 py-3 border-b border-surface-100 w-32 text-green-600">{{ __('Profit') }}</th>
                                <th class="px-4 py-3 border-b border-surface-100 w-40">{{ __('Total') }}</th>
                                <th class="px-4 py-3 border-b border-surface-100 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-50">
                            @forelse($items as $index => $item)
                                <tr class="hover:bg-surface-50/30 transition-colors">
                                    <td class="px-4 py-4 text-surface-400 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-surface-900">{{ $item['product_name'] }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <select wire:model.live="items.{{ $index }}.product_unit_id" @if($isReadOnly) disabled @endif class="w-full px-2 py-1.5 rounded-lg border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none text-xs @error('items.'.$index.'.product_unit_id') border-red-500 @enderror disabled:bg-surface-50">
                                            @foreach($item['units'] as $unit)
                                                <option value="{{ $unit['id'] }}">{{ $unit['unit_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="number" step="1" wire:model.live="items.{{ $index }}.quantity" @if($isReadOnly) disabled @endif 
                                            class="w-full px-3 py-1.5 rounded-lg border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none font-bold text-center @error('items.'.$index.'.quantity') border-red-500 @enderror disabled:bg-surface-50">
                                    </td>
                                    <td class="px-4 py-4 text-left">
                                        <input type="number" step="1" wire:model.live="items.{{ $index }}.unit_price" @if($isReadOnly) disabled @endif 
                                            class="w-full px-3 py-1.5 rounded-lg border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none font-bold text-center disabled:bg-surface-50">
                                    </td>
                                    <td class="px-4 py-4 text-left font-bold text-green-600 text-lg">
                                        {{ number_format($item['profit'], 0) }}
                                    </td>
                                    <td class="px-4 py-4 text-left font-bold text-surface-900 border-r border-surface-50">
                                        {{ number_format($item['total'], 0) }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @if(!$isReadOnly)
                                            <button type="button" wire:click="removeItem({{ $index }})" class="p-1.5 text-red-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-surface-400 italic">
                                        {{ __('No products added to the invoice yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-6">
                    {{-- Notes & Payment --}}
                    <div>
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-semibold text-surface-700">{{ __('Invoice Notes') }}</label>
                            <textarea wire:model="notes" @if($isReadOnly) disabled @endif rows="2" class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none resize-none disabled:bg-surface-50" placeholder="{{ __('Extra details...') }}"></textarea>
                        </div>
                        <div class="p-4 bg-surface-50 rounded-2xl border border-surface-100">
                            <h4 class="font-bold text-surface-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                {{ __('Payment Details') }}
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 text-xs font-bold text-surface-500 uppercase tracking-wider">{{ __('Paid Amount') }}</label>
                                    <input type="number" step="1" wire:model.live="paid_amount" @if($isReadOnly) disabled @endif class="w-full px-4 py-2 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none font-bold text-lg text-primary-600 disabled:bg-surface-50">
                                </div>
                                <div>
                                    <label class="block mb-1 text-xs font-bold text-surface-500 uppercase tracking-wider">{{ __('Remaining') }}</label>
                                    <div class="px-4 py-2 bg-white rounded-xl border border-surface-200 font-bold text-lg text-red-500">
                                        {{ number_format($total_due - $paid_amount, 0) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Summary Totals --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-green-600 px-3 py-2.5 bg-green-50/50 rounded-2xl border border-green-100/50 mb-4 shadow-sm">
                            <span class="font-bold flex items-center gap-2 text-sm uppercase tracking-wider">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                {{ __('Net Profit') }}
                            </span>
                            <span class="font-black text-xl">{{ number_format($total_profit, 0) }} <span class="text-xs font-bold uppercase">EGP</span></span>
                        </div>
                        <div class="flex items-center justify-between px-2 gap-4 text-sm">
                            <span class="text-surface-600">{{ __('Subtotal') }}</span>
                            <span class="font-bold">{{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between px-2 gap-4 text-sm">
                            <span class="text-surface-600">{{ __('Previous Balance') }}</span>
                            <span class="font-bold text-red-600">{{ number_format($customer_balance, 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between px-2 gap-4 bg-surface-100 p-2 rounded-lg">
                            <span class="text-surface-900 font-bold">{{ __('Gross Total Due') }}</span>
                            <span class="font-bold text-lg text-primary-700">{{ number_format($total_due, 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between px-2 gap-4 text-sm">
                            <span class="text-surface-600">{{ __('Tax (%)') }}</span>
                            <div class="flex items-center gap-2">
                                <input type="number" step="1" wire:model.live="tax_percentage" @if($isReadOnly) disabled @endif class="w-20 px-2 py-1 rounded-lg border border-surface-200 text-center outline-none focus:ring-2 focus:ring-primary-500/20 font-bold disabled:bg-surface-50">
                                <span class="font-bold w-24 text-left">{{ number_format($tax_amount, 0) }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-2 gap-4 text-sm">
                            <span class="text-surface-600">{{ __('Discount') }}</span>
                            <input type="number" step="1" wire:model.live="discount_amount" @if($isReadOnly) disabled @endif class="w-44 px-3 py-1 rounded-lg border border-surface-200 text-left outline-none focus:ring-2 focus:ring-primary-500/20 font-bold disabled:bg-surface-50">
                        </div>
                        <div class="flex items-center justify-between px-2 gap-4 pb-2 border-b border-surface-100 text-sm">
                            <span class="text-surface-600">{{ __('Shipping') }}</span>
                            <input type="number" step="1" wire:model.live="shipping_cost" @if($isReadOnly) disabled @endif class="w-44 px-3 py-1 rounded-lg border border-surface-200 text-left outline-none focus:ring-2 focus:ring-primary-500/20 font-bold disabled:bg-surface-50">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col @if($total_amount - $paid_amount < 0) bg-green-600 @else bg-primary-600 @endif text-white p-4 rounded-2xl shadow-lg shadow-primary-500/30">
                                <span class="text-sm font-bold opacity-80 mb-1 uppercase tracking-wider">{{ __('Invoice Remaining') }}</span>
                                <span class="text-2xl font-black">{{ number_format($total_amount - $paid_amount, 0) }} <span class="text-xs">{{ __('EGP') }}</span></span>
                            </div>
                            <div class="flex flex-col bg-red-600 text-white p-4 rounded-2xl shadow-lg shadow-red-500/30">
                                <span class="text-sm font-bold opacity-80 mb-1 uppercase tracking-wider">{{ __('Final Remaining') }}</span>
                                <span class="text-2xl font-black">{{ number_format($total_due - $paid_amount, 0) }} <span class="text-xs">{{ __('EGP') }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-end gap-6 pt-6 border-t border-surface-100">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" wire:model="should_print" class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500 transition-all">
                        <span class="text-sm font-bold text-surface-700 group-hover:text-primary-600 transition-colors">
                            <i class="fa-solid fa-print mr-1"></i> {{ __('Print Directly') }}
                        </span>
                    </label>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="show = false" class="px-6 py-2.5 rounded-xl border border-surface-200 text-surface-600 font-bold hover:bg-surface-50 transition-all">
                            {{ $isReadOnly ? __('Close') : __('Cancel') }}
                        </button>
                        @if(!$isReadOnly)
                            <button type="submit" wire:loading.attr="disabled" class="px-8 py-2.5 rounded-xl bg-primary-600 text-white font-bold hover:bg-primary-700 shadow-lg shadow-primary-500/30 transition-all flex items-center gap-2">
                                <span wire:loading.remove wire:target="save">{{ $isEdit ? __('Update Invoice') : __('Save & Issue Invoice') }}</span>
                                <span wire:loading wire:target="save">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('print-invoice-direct', (data) => {
        const invoiceId = data[0].invoiceId;
        const printUrl = `/dashboard/sales/${invoiceId}/print?direct=1`;
        
        // Open in a new small window for auto-printing
        const printWindow = window.open(printUrl, '_blank', 'width=400,height=600');
        
        if (printWindow) {
            // Success notification or handle as needed
        } else {
            // Popup blocked: Fallback - open in new tab
            window.open(printUrl, '_blank');
        }
    });

    $wire.on('open-print-modal', (data) => {
        const invoiceId = data[0].invoiceId;
        const printUrl = `/dashboard/sales/${invoiceId}/print`;
        window.open(printUrl, '_blank');
    });
</script>
@endscript
