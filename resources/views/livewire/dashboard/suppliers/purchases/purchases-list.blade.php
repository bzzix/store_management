<div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                window.addEventListener('notify', event => {
                    const { type, message, title } = event.detail;
                    iziToast[type]({
                        title: title || '',
                        message: message,
                        position: 'topRight',
                        rtl: true
                    });
                });

                window.addEventListener('open-delete-modal', event => {
                    const { invoiceId, invoiceNumber } = event.detail;
                    
                    iziToast.question({
                        timeout: 20000,
                        layout: 2,
                        title: '{{ __("Delete Invoice") }}',
                        message: `{{ __('Do you really want to delete this invoice') }}: <strong>${invoiceNumber}</strong>?`,
                        rtl: true,
                        overlay: true,
                        displayMode: 'once',
                        id: 'delete-question',
                        zindex: 999999999,
                        position: 'center',
                        buttons: [
                            ['<button><b>{{ __("Delete") }}</b></button>', function (instance, toast) {
                                Livewire.find('{{ $this->getId() }}').openDeleteModal(invoiceId, invoiceNumber);
                                Livewire.find('{{ $this->getId() }}').confirmDeleteInvoice();
                                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            }, true],
                            ['<button>{{ __("Cancel") }}</button>', function (instance, toast) {
                                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            }],
                        ]
                    });
                });
            });
        </script>
    @endpush

    {{-- Main Container --}}
    <div class="space-y-6">
        {{-- List Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-surface-100">
            <div>
                <h2 class="text-2xl font-bold text-surface-900">{{ __('Purchase Invoices') }}</h2>
                <p class="text-surface-500 text-sm mt-1">{{ __('Manage your purchases and supplier invoices') }}</p>
            </div>
            
            @can('purchase_invoices_create')
                <button wire:click="openCreateModal" 
                    class="flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-600/20 transition-all active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Add Purchase Invoice') }}
                </button>
            @endcan
        </div>

        {{-- Filters & DataTable --}}
        <div class="bg-white rounded-2xl shadow-sm border border-surface-100 overflow-hidden">
            <div class="p-6">
                <livewire:dashboard.suppliers.purchases.purchases-data-table />
            </div>
        </div>
    </div>

    {{-- Create / Edit Invoice Modal --}}
    <div x-data="{ show: @entangle('showInvoiceModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-surface-900/50 backdrop-blur-sm" @click="show = false"></div>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="inline-block w-full max-w-6xl overflow-hidden text-right align-bottom transition-all transform bg-white shadow-2xl rounded-2xl sm:my-8 sm:align-middle">
                
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-surface-100 bg-surface-50/50 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-surface-900">
                        {{ $isEditing ? __('Edit Purchase Invoice') : __('New Purchase Invoice') }}
                    </h3>
                    <button @click="show = false" class="p-2 text-surface-400 hover:text-surface-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-6 overflow-y-auto max-h-[calc(100vh-200px)]">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        {{-- Supplier --}}
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-semibold text-surface-700">{{ __('Supplier') }}</label>
                            <select wire:model.live="supplier_id" class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
                                <option value="">{{ __('Select Supplier') }}</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            
                            @if($supplier_id)
                                <div class="mt-2 text-sm font-black flex justify-between items-center p-3 rounded-xl bg-surface-50 border border-surface-100 text-surface-600 shadow-inner">
                                    <span>{{ __('Supplier Current Balance') }}:</span>
                                    <span class="text-primary-600">{{ number_format($previous_balance, 0) }} EGP</span>
                                </div>
                            @endif
                        </div>

                        {{-- Warehouse --}}
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-surface-700">{{ __('Warehouse') }}</label>
                            <select wire:model.live="warehouse_id" class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
                                <option value="">{{ __('Select Warehouse') }}</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Date --}}
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-surface-700">{{ __('Invoice Date') }}</label>
                            <input type="date" wire:model.live="invoice_date" class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none">
                            @error('invoice_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Product Search --}}
                    <div class="relative mb-8">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-surface-400">
                            <svg wire:loading.remove wire:target="productSearch" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <div wire:loading wire:target="productSearch" class="w-5 h-5 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="productSearch" placeholder="{{ __('Search products by name, SKU or barcode...') }}" 
                            class="w-full pr-12 pl-4 py-3.5 rounded-2xl border-2 border-primary-50 px-6 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none text-lg">
                        
                        @if(!empty($productSearch) && count($searchResults) > 0)
                            <div class="absolute z-50 w-full mt-2 bg-white border border-surface-100 rounded-2xl shadow-2xl max-h-80 overflow-y-auto">
                                @foreach($searchResults as $product)
                                    <button type="button" wire:click="addProductFromSearch({{ $product->id }})" class="w-full px-6 py-4 text-right hover:bg-primary-50 flex items-center justify-between border-b border-surface-50 last:border-0 transition-colors group">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-surface-100 flex items-center justify-center font-bold text-surface-500 group-hover:bg-primary-100 group-hover:text-primary-600 transition-colors">
                                                {{ substr($product->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-surface-900">{{ $product->name }}</div>
                                                <div class="text-xs text-surface-500">SKU: {{ $product->sku }}</div>
                                            </div>
                                        </div>
                                        <div class="text-left bg-primary-50 px-3 py-1 rounded-lg">
                                            <div class="text-primary-700 font-bold fs-5">{{ number_format($product->current_cost_price ?? 0, 0) }} EGP</div>
                                            @php
                                                $availableStock = $product->warehouseStock->sum('quantity');
                                            @endphp
                                            <div class="text-[10px] {{ $availableStock > 0 ? 'text-green-600' : 'text-red-600' }} font-bold">
                                                {{ __('Available') }}: {{ number_format($availableStock, 0) }} {{ $product->base_unit }}
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @error('items') <div class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</div> @enderror
                    </div>

                    {{-- Items Table --}}
                    <div class="border border-surface-100 rounded-2xl overflow-hidden mb-8 shadow-sm">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-surface-50/80 text-surface-600 font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 border-b border-surface-100">#</th>
                                    <th class="px-6 py-4 border-b border-surface-100">{{ __('Product') }}</th>
                                    <th class="px-6 py-4 border-b border-surface-100 w-32 text-center">{{ __('Quantity') }}</th>
                                    <th class="px-6 py-4 border-b border-surface-100 w-40 text-center">{{ __('Purchase Price') }}</th>
                                    <th class="px-6 py-4 border-b border-surface-100 w-32 text-center">{{ __('Tax') }}</th>
                                    <th class="px-6 py-4 border-b border-surface-100 w-32 text-center">{{ __('Discount') }}</th>
                                    <th class="px-6 py-4 border-b border-surface-100 w-40 text-left">{{ __('Row Total') }}</th>
                                    <th class="px-6 py-4 border-b border-surface-100 w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-50">
                                @forelse($items as $index => $item)
                                    <tr class="hover:bg-surface-50/30 transition-colors">
                                        <td class="px-6 py-4 text-surface-400 font-medium">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-surface-900">{{ $item['product_name'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <input type="number" step="1" wire:model.live.debounce.500ms="items.{{ $index }}.quantity" wire:change="updateItemRow" 
                                                class="w-full px-3 py-2 rounded-lg border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none font-bold text-center">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <input type="number" step="1" wire:model.live.debounce.500ms="items.{{ $index }}.unit_price" wire:change="updateItemRow" 
                                                class="w-full px-3 py-2 rounded-lg border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none font-bold text-center">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <input type="number" step="1" wire:model.live.debounce.500ms="items.{{ $index }}.tax_amount" wire:change="updateItemRow" 
                                                class="w-full px-3 py-2 rounded-lg border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none font-bold text-center text-red-500">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <input type="number" step="1" wire:model.live.debounce.500ms="items.{{ $index }}.discount_amount" wire:change="updateItemRow" 
                                                class="w-full px-3 py-2 rounded-lg border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none font-bold text-center text-green-600">
                                        </td>
                                        <td class="px-6 py-4 text-left font-black text-surface-900 border-r border-surface-50 bg-surface-50/20">
                                            {{ number_format($item['total'] ?? 0, 0) }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button type="button" wire:click="removeProductFromInvoice({{ $index }})" class="p-2 text-red-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-surface-400 italic">
                                            <div class="flex flex-col items-center gap-2">
                                                <svg class="w-12 h-12 text-surface-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                {{ __('No products added to the invoice yet.') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals Section --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-6">
                        {{-- Notes & Payment --}}
                        <div>
                            <div class="mb-4">
                                <label class="block mb-2 text-sm font-semibold text-surface-700">{{ __('Invoice Notes') }}</label>
                                <textarea wire:model.live="notes" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none resize-none" placeholder="{{ __('Extra details about this purchase...') }}"></textarea>
                            </div>

                            <div class="mb-4 p-4 bg-surface-50 rounded-2xl border border-surface-100">
                                <label class="block mb-3 text-sm font-bold text-surface-700 uppercase tracking-wider">{{ __('Invoice Image Attachment') }}</label>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <input type="file" wire:model="image" id="invoice_image" class="hidden">
                                        <label for="invoice_image" class="flex flex-col items-center justify-center border-2 border-dashed border-surface-200 rounded-xl p-3 hover:border-primary-500 hover:bg-white transition-all cursor-pointer">
                                            <span class="text-xs text-surface-500 font-medium">{{ __('Click to upload image') }}</span>
                                        </label>
                                    </div>
                                    @if ($image)
                                        <div class="relative group">
                                            <img src="{{ $image->temporaryUrl() }}" class="w-16 h-16 object-cover rounded-xl border border-surface-200 shadow-sm">
                                            <div class="absolute inset-0 bg-black/40 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <button wire:click="$set('image', null)" type="button" class="text-white bg-red-500 p-1.5 rounded-lg shadow-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div wire:loading wire:target="image" class="text-primary-600 mt-2 text-xs font-bold animate-pulse">{{ __('Uploading image...') }}</div>
                                @error('image') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="p-4 bg-surface-50 rounded-2xl border border-surface-100">
                                <h4 class="font-bold text-surface-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm-5-4l-3-3m0 0l3-3m-3 3h8"></path></svg>
                                    {{ __('Payment Details') }}
                                </h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-1 text-xs font-bold text-surface-500 uppercase tracking-wider">{{ __('Paid Amount') }}</label>
                                        <input type="number" step="1" wire:model.live.debounce.500ms="paid_amount" class="w-full px-4 py-2 rounded-xl border border-surface-200 focus:ring-2 focus:ring-primary-500/20 outline-none font-bold text-lg text-primary-600 disabled:bg-surface-50">
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-xs font-bold text-surface-500 uppercase tracking-wider">{{ __('Remaining') }}</label>
                                        <div class="px-4 py-2 bg-white rounded-xl border border-surface-200 font-bold text-lg text-red-500">
                                            {{ number_format(((float)$total_amount + (float)$previous_balance) - (float)($paid_amount ?: 0), 0) }}
                                        </div>
                                    </div>
                                </div>
                                @error('paid_amount') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Summary Totals --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-surface-600 px-3 py-2.5 bg-surface-50/50 rounded-2xl border border-surface-100 mb-4 shadow-sm">
                                <span class="font-bold flex items-center gap-2 text-sm uppercase tracking-wider">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    {{ __('Invoice Summary') }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between px-2 gap-4 text-sm">
                                <span class="text-surface-600">{{ __('Subtotal') }}</span>
                                <span class="font-bold">{{ number_format($subtotal, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between px-2 gap-4 text-sm">
                                <span class="text-surface-600">{{ __('Supplier Previous Balance') }}</span>
                                <span class="font-bold text-red-600">{{ number_format($previous_balance, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between px-2 gap-4 bg-surface-100 p-2 rounded-lg">
                                <span class="text-surface-900 font-bold">{{ __('Gross Total Required') }}</span>
                                <span class="font-bold text-lg text-primary-700">{{ number_format((float)$total_amount + (float)$previous_balance, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between px-2 gap-4 text-sm mt-2">
                                <span class="text-surface-600">{{ __('Global Tax') }} (+)</span>
                                <div class="flex items-center gap-2">
                                    <input type="number" step="1" wire:model.live.debounce.500ms="tax_amount" wire:change="calculateTotals" class="w-32 px-2 py-1.5 rounded-lg border border-surface-200 text-center outline-none focus:ring-2 focus:ring-primary-500/20 font-bold text-red-600">
                                </div>
                            </div>
                            <div class="flex items-center justify-between px-2 gap-4 pb-2 border-b border-surface-100 text-sm">
                                <span class="text-surface-600">{{ __('Global Discount') }} (-)</span>
                                <input type="number" step="1" wire:model.live.debounce.500ms="discount_amount" wire:change="calculateTotals" class="w-32 px-3 py-1.5 rounded-lg border border-surface-200 text-center outline-none focus:ring-2 focus:ring-primary-500/20 font-bold text-green-600">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <div class="flex flex-col @if((float)$total_amount - (float)($paid_amount ?: 0) < 0) bg-green-600 @else bg-primary-600 @endif text-white p-4 rounded-2xl shadow-lg shadow-primary-500/30">
                                    <span class="text-sm font-bold opacity-80 mb-1 uppercase tracking-wider">{{ __('Invoice Remaining') }}</span>
                                    <span class="text-2xl font-black">{{ number_format((float)$total_amount - (float)($paid_amount ?: 0), 0) }} <span class="text-xs">{{ __('EGP') }}</span></span>
                                </div>
                                <div class="flex flex-col bg-red-600 text-white p-4 rounded-2xl shadow-lg shadow-red-500/30">
                                    <span class="text-sm font-bold opacity-80 mb-1 uppercase tracking-wider">{{ __('Final Remaining') }}</span>
                                    <span class="text-2xl font-black">{{ number_format(((float)$total_amount + (float)$previous_balance) - (float)($paid_amount ?: 0), 0) }} <span class="text-xs">{{ __('EGP') }}</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-surface-100 bg-surface-50/50 flex items-center justify-end gap-3">
                    <button type="button" @click="show = false" class="px-6 py-2.5 rounded-xl border border-surface-200 text-surface-600 font-bold hover:bg-surface-100 transition-all">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="saveInvoice" wire:loading.attr="disabled" 
                        class="px-10 py-2.5 rounded-xl bg-primary-600 text-white font-bold hover:bg-primary-700 shadow-lg shadow-primary-600/30 transition-all active:scale-95 flex items-center gap-2">
                        <span wire:loading.remove wire:target="saveInvoice">{{ __('Save & Approve Invoice') }}</span>
                        <span wire:loading wire:target="saveInvoice" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ __('Processing...') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- View / Print Invoice Modal --}}
    @if($viewInvoice)
    <div x-data="{ showPrint: @entangle('showPrintModal') }" x-show="showPrint" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showPrint" class="fixed inset-0 transition-opacity bg-surface-900/60 backdrop-blur-md"></div>
            
            <div x-show="showPrint" x-transition:enter="ease-out duration-300" 
                class="inline-block w-full max-w-4xl overflow-hidden text-right align-bottom transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8 sm:align-middle">
                
                <div class="modal-header d-print-none px-8 py-5 border-b border-surface-100 flex items-center justify-between bg-surface-50/30">
                    <h3 class="text-xl font-black text-surface-900">{{ __('View Invoice') }} <span class="text-primary-600 ml-2">#{{ $viewInvoice->invoice_number }}</span></h3>
                    <button type="button" class="p-2 text-surface-400 hover:text-red-500 transition-colors" wire:click="closePrintModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="modal-body p-10 print-area" id="print-area">
                    {{-- Header for Print --}}
                    <div class="text-center mb-10 pb-6 border-b-4 border-double border-surface-200">
                        <h1 class="text-4xl font-black text-surface-900 tracking-tighter">{{ get_setting('appName', 'أولاد عبد الستار للأعلاف') }}</h1>
                        <p class="text-surface-500 font-bold uppercase tracking-widest mt-2">{{ __('Purchase Invoice') }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-8 mb-10">
                        <div class="space-y-3">
                            <div class="flex gap-2"><span class="font-bold text-surface-500">{{ __('Invoice Number') }}:</span> <span class="font-black text-primary-600">#{{ $viewInvoice->invoice_number }}</span></div>
                            <div class="flex gap-2"><span class="font-bold text-surface-500">{{ __('Supplier') }}:</span> <span class="font-bold text-surface-900">{{ $viewInvoice->supplier->name ?? '' }}</span></div>
                            <div class="flex gap-2"><span class="font-bold text-surface-500">{{ __('Warehouse') }}:</span> <span class="font-bold text-surface-900">{{ $viewInvoice->warehouse->name ?? '' }}</span></div>
                        </div>
                        <div class="flex flex-col items-end gap-3">
                            <div class="flex gap-2 items-center">
                                <span class="font-bold text-surface-500">حالة الفاتورة:</span>
                                @php
                                    $statusColors = ['draft' => 'surface-200 text-surface-700', 'pending' => 'yellow-100 text-yellow-700', 'completed' => 'green-100 text-green-700', 'cancelled' => 'red-100 text-red-700'];
                                    $statusLabels = ['draft' => 'مسودة', 'pending' => 'قيد الانتظار', 'completed' => 'مكتملة', 'cancelled' => 'ملغاة'];
                                    $sClass = $statusColors[$viewInvoice->status] ?? 'surface-100 text-surface-600';
                                    $sLabel = $statusLabels[$viewInvoice->status] ?? $viewInvoice->status;
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-{{ $sClass }}">{{ $sLabel }}</span>
                            </div>
                            <div class="flex gap-2 items-center">
                                <span class="font-bold text-surface-500">حالة الدفع:</span>
                                @php
                                    $payStatusColors = ['unpaid' => 'red-100 text-red-700', 'partial' => 'yellow-100 text-yellow-700', 'paid' => 'green-100 text-green-700'];
                                    $payLabels = ['unpaid' => 'غير مدفوعة', 'partial' => 'دفع جزئي', 'paid' => 'مدفوعة'];
                                    $pClass = $payStatusColors[$viewInvoice->payment_status] ?? 'surface-100 text-surface-600';
                                    $pLabel = $payLabels[$viewInvoice->payment_status] ?? $viewInvoice->payment_status;
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-{{ $pClass }}">{{ $pLabel }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-2 border-surface-900 rounded-3xl overflow-hidden mb-10 shadow-sm">
                        <table class="w-full text-sm text-center">
                            <thead class="bg-surface-900 text-white">
                                <tr>
                                    <th class="px-4 py-4 border-l border-white/20">#</th>
                                    <th class="px-4 py-4 border-l border-white/20">المنتج</th>
                                    <th class="px-4 py-4 border-l border-white/20">الكمية</th>
                                    <th class="px-4 py-4 border-l border-white/20">الوزن</th>
                                    <th class="px-4 py-4 border-l border-white/20">سعر الوحدة</th>
                                    <th class="px-4 py-4 border-l border-white/20">الضريبة</th>
                                    <th class="px-4 py-4 border-l border-white/20">الخصم</th>
                                    <th class="px-4 py-4">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-200">
                                @php $totalWeight = 0; @endphp
                                @foreach($viewInvoice->items as $index => $item)
                                @php
                                    $weight = $item->quantity * ($item->product->weight ?? 1); 
                                    $totalWeight += $weight;
                                @endphp
                                <tr class="bg-white">
                                    <td class="px-4 py-4 border-l border-surface-100">{{ $index + 1 }}</td>
                                    <td class="px-4 py-4 border-l border-surface-100 font-bold text-surface-900">{{ $item->product->name ?? '' }}</td>
                                    <td class="px-4 py-4 border-l border-surface-100 font-black">{{ number_format($item->quantity, 0) }}</td>
                                    <td class="px-4 py-4 border-l border-surface-100">{{ number_format($weight, 0) }} كجم</td>
                                    <td class="px-4 py-4 border-l border-surface-100">{{ number_format($item->unit_price, 0) }}</td>
                                    <td class="px-4 py-4 border-l border-surface-100 text-red-500 font-bold">{{ number_format($item->tax_amount, 0) }}</td>
                                    <td class="px-4 py-4 border-l border-surface-100 text-green-600 font-bold">{{ number_format($item->discount_amount, 0) }}</td>
                                    <td class="px-4 py-4 font-black bg-surface-50">{{ number_format($item->total, 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-surface-50 font-black border-t-2 border-surface-900">
                                <tr>
                                    <td colspan="2" class="px-4 py-4 text-right">الإجمالي الكلي للوزن:</td>
                                    <td colspan="6" class="px-4 py-4 text-left">{{ number_format($totalWeight, 0) }} كجم</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="grid grid-cols-2 gap-12">
                        <div class="space-y-6">
                            @if($viewInvoice->notes)
                                <div class="bg-surface-50 p-6 rounded-3xl border border-surface-100">
                                    <h5 class="text-sm font-black text-surface-900 uppercase border-b border-surface-200 pb-2 mb-3">ملاحظات الفاتورة</h5>
                                    <p class="text-surface-600 text-sm leading-relaxed">{{ $viewInvoice->notes }}</p>
                                </div>
                            @endif
                            @if($viewInvoice->image)
                                <div class="bg-surface-50 p-6 rounded-3xl border border-surface-100">
                                    <h5 class="text-sm font-black text-surface-900 uppercase border-b border-surface-200 pb-2 mb-3">صورة الفاتورة المرفقة</h5>
                                    <img src="{{ Storage::url($viewInvoice->image) }}" class="w-full h-auto rounded-2xl shadow-sm border border-surface-200">
                                </div>
                            @endif
                        </div>
                        
                        <div class="bg-surface-900 text-white p-8 rounded-[40px] space-y-4 shadow-2xl relative overflow-hidden">
                            {{-- Decor --}}
                            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full"></div>
                            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white/5 rounded-full"></div>

                            <div class="flex justify-between items-center text-sm opacity-60">
                                <span>الإجمالي الفرعي:</span>
                                <span class="font-bold">{{ number_format($viewInvoice->subtotal, 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm opacity-60">
                                <span>إجمالي الضريبة (+):</span>
                                <span class="font-bold text-red-400">{{ number_format($viewInvoice->tax_amount, 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm opacity-60">
                                <span>إجمالي الخصم (-):</span>
                                <span class="font-bold text-green-400">{{ number_format($viewInvoice->discount_amount, 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-4 border-y border-white/10">
                                <span class="font-bold fs-3">إجمالي الحساب:</span>
                                <span class="font-black text-3xl text-primary-400">{{ number_format($viewInvoice->total_amount, 0) }} <span class="text-xs uppercase">EGP</span></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="opacity-60">المدفوع حالياً:</span>
                                <span class="font-black text-lg text-green-400">{{ number_format($viewInvoice->paid_amount, 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t border-white/20">
                                <span class="font-black text-xl text-red-400">الرصيد المتبقي:</span>
                                <span class="font-black text-4xl underline decoration-red-500/50 underline-offset-8">{{ number_format($viewInvoice->total_amount - $viewInvoice->paid_amount, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-print-none px-8 py-6 bg-surface-50 border-t border-surface-100 flex justify-end gap-3">
                    <button type="button" class="px-6 py-2.5 rounded-xl border border-surface-200 text-surface-600 font-bold hover:bg-surface-100 transition-all" wire:click="closePrintModal">{{ __('Close') }}</button>
                    
                    <button type="button" class="px-8 py-2.5 rounded-xl bg-primary-600 text-white font-bold hover:bg-primary-700 shadow-lg shadow-primary-600/30 flex items-center gap-2 transition-all active:scale-95" 
                        onclick="window.open('{{ route('dashboard.suppliers.purchases.print', $viewInvoice->id) }}', '_blank', 'width=400,height=600')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2h-14a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm-5-4l-3-3m0 0l3-3m-3 3h8"></path></svg>
                        {{ __('Print 80mm') }}
                    </button>

                    <button type="button" class="px-8 py-2.5 rounded-xl border-2 border-surface-200 text-surface-600 font-bold hover:bg-surface-50 flex items-center gap-2 transition-all active:scale-95" onclick="printModalArea()">
                        {{ __('Print A4 / Full') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function printModalArea() {
        var printContents = document.getElementById('print-area').innerHTML;
        var originalContents = document.body.innerHTML;
        
        document.body.innerHTML = printContents;
        
        // Add minimal styling just for print
        var style = document.createElement('style');
        style.innerHTML = `
            @media print {
                body { margin: 0; padding: 20px; font-family: 'Tajawal', sans-serif; direction: rtl; }
                .d-print-none { display: none !important; }
                .print-area { width: 100% !important; padding: 0 !important; }
                .bg-surface-900 { background-color: #111827 !important; -webkit-print-color-adjust: exact; }
                .text-white { color: #ffffff !important; -webkit-print-color-adjust: exact; }
                .text-primary-400 { color: #60a5fa !important; -webkit-print-color-adjust: exact; }
            }
        `;
        document.head.appendChild(style);

        window.print();

        document.body.innerHTML = originalContents;
        window.location.reload(); 
    }
</script>
@endpush
