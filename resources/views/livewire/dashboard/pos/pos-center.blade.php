@inject('pricingService', 'App\Services\PricingService')
<div class="space-y-6">
    {{-- Header & Mode Switcher --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-surface-900 flex items-center gap-2">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.1 5.5a2 2 0 002 2.5h11a2 2 0 002-2.5L17 13M9 21h6"></path></svg>
                {{ __('مركز المبيعات السريع (POS)') }}
            </h3>
            <p class="text-surface-500 mt-1">{{ __('إدارة مبيعاتك ومشترياتك من خلال واجهة سريعة وموحدة.') }}</p>
        </div>

        <div class="flex items-center bg-surface-100 p-1 rounded-2xl w-fit self-center lg:self-auto border border-surface-200/50 shadow-inner">
            <button wire:click="$set('mode', 'sale')" 
                class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $mode === 'sale' ? 'bg-white text-primary-600 shadow-sm' : 'text-surface-500 hover:text-surface-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                {{ __('وضع البيع') }}
            </button>
            <button wire:click="$set('mode', 'purchase')" 
                class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $mode === 'purchase' ? 'bg-white text-success-600 shadow-sm' : 'text-surface-500 hover:text-surface-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                {{ __('وضع الشراء') }}
            </button>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-6 border-b border-surface-100 pb-px overflow-x-auto scrollbar-hide">
        <button wire:click="$set('activeTab', 'pos')" 
            class="pb-4 text-sm font-bold transition-all relative {{ $activeTab === 'pos' ? 'text-primary-600' : 'text-surface-400 hover:text-surface-600' }}">
            {{ __('عملية جديدة') }}
            @if($activeTab === 'pos') <span class="absolute bottom-0 left-0 right-0 h-1 bg-primary-600 rounded-t-full"></span> @endif
        </button>
        <button wire:click="$set('activeTab', 'history')" 
            class="pb-4 text-sm font-bold transition-all relative {{ $activeTab === 'history' ? 'text-primary-600' : 'text-surface-400 hover:text-surface-600' }}">
            {{ __('سجل العمليات') }}
            @if($activeTab === 'history') <span class="absolute bottom-0 left-0 right-0 h-1 bg-primary-600 rounded-t-full"></span> @endif
        </button>
    </div>

    @if($activeTab === 'pos')
        <div class="grid grid-cols-12 gap-6 items-start">
            {{-- Right: Product Catalog (5 cols) --}}
            <div class="col-span-12 lg:col-span-5 space-y-6">
                {{-- Search & Controls --}}
                <div class="bg-white p-4 rounded-3xl border border-surface-200/60 shadow-soft flex flex-col md:flex-row gap-4 items-center">
                    <div class="relative flex-1 group">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-surface-400 group-focus-within:text-primary-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" 
                            class="w-full bg-surface-50 border border-surface-200 rounded-2xl pr-11 pl-4 py-3 text-right text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all" 
                            placeholder="{{ __('البحث باسم المنتج، الكود أو الباركود...') }}">
                    </div>
                    
                    <div class="flex items-center bg-surface-100 p-1 rounded-xl border border-surface-200">
                        <button wire:click="$set('viewMode', 'grid')" class="p-2 rounded-lg transition-all {{ $viewMode === 'grid' ? 'bg-white text-primary-600 shadow-sm' : 'text-surface-400' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </button>
                        <button wire:click="$set('viewMode', 'list')" class="p-2 rounded-lg transition-all {{ $viewMode === 'list' ? 'bg-white text-primary-600 shadow-sm' : 'text-surface-400' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Products Grid / List --}}
                @if($viewMode === 'grid')
                    {{-- 2 Products per row always --}}
                    <div class="grid grid-cols-2 gap-4">
                        @forelse($products as $product)
                            <div wire:click="addToCart({{ $product->id }})" 
                                class="bg-white border-2 border-surface-100 hover:border-primary-400 rounded-3xl p-4 shadow-sm hover:shadow-xl transition-all group cursor-pointer active:scale-95 flex flex-col justify-between">
                                
                                <div class="relative mb-3">
                                    <div class="aspect-auto h-32 w-full bg-surface-50 rounded-2xl overflow-hidden">
                                        @if($product->main_image)
                                            <img src="{{ storage_url($product->main_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-surface-200">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @php
                                        $stock = $product->warehouseStock->first()?->quantity ?? 0;
                                        $stockColor = $stock > 10 ? 'bg-success-100 text-success-700 border-success-200' : ($stock > 0 ? 'bg-warning-100 text-warning-700 border-warning-200' : 'bg-red-100 text-red-700 border-red-200');
                                    @endphp
                                    <div class="absolute top-2 right-2 px-2.5 py-1 rounded-lg border {{ $stockColor }} text-[10px] font-black shadow-sm backdrop-blur-md">
                                        {{ $stock }} {{ $product->base_unit }}
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <h4 class="text-xs font-extrabold text-surface-900 group-hover:text-primary-600 transition-colors line-clamp-2 text-right mb-1">
                                        {{ $product->name }}
                                    </h4>
                                    <p class="text-[10px] font-bold text-surface-400 text-right">#{{ $product->barcode ?? $product->sku }}</p>
                                </div>

                                <div class="mt-3 space-y-1 p-2 bg-surface-50 rounded-xl border border-surface-100">
                                    <div class="flex items-center justify-between text-[10px] font-bold text-surface-500">
                                        <span>{{ __('التكلفة') }}</span>
                                        <span class="text-surface-900">{{ number_format($product->current_cost_price, 2) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] font-bold text-surface-500">
                                        <span>{{ __('البيع') }}</span>
                                        @php
                                            // Fetch real cash selling price using PricingService
                                            $priceData = app(\App\Services\PricingService::class)->safeCalculate($product->purchase_price ?? $product->current_cost_price ?? 0, 'cash');
                                            $cashSellingPrice = $priceData['final_price'] ?? 0;
                                        @endphp
                                        <span class="text-surface-900">{{ number_format($cashSellingPrice, 2) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] font-black pt-1.5 mt-1.5 border-t border-surface-200">
                                        <span class="text-primary-600">{{ __('الربح') }}</span>
                                        @php $profit = $cashSellingPrice - ($product->current_cost_price ?? 0); @endphp
                                        <span class="{{ $profit > 0 ? 'text-success-600' : 'text-red-500' }}" dir="ltr">{{ number_format($profit, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-20 text-center">
                                <div class="inline-flex p-6 bg-surface-50 rounded-full mb-4 text-surface-300">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-3.586 3.586a2 2 0 11-2.828-2.828L16 14m-2 2l1.586 1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <h3 class="text-lg font-bold text-surface-900">{{ __('لم يتم العثور على منتجات') }}</h3>
                                <p class="text-surface-500">{{ __('حاول تعديل شروط البحث.') }}</p>
                            </div>
                        @endforelse
                    </div>
                @else
                    <div class="space-y-2">
                        @forelse($products as $product)
                            <div wire:click="addToCart({{ $product->id }})" 
                                class="bg-white border-2 border-surface-100 hover:border-primary-400 rounded-2xl p-3 shadow-sm hover:shadow-md transition-all flex items-center justify-between group cursor-pointer active:scale-[0.99]">
                                <div class="flex items-center gap-4 text-right">
                                    <div class="w-14 h-14 bg-surface-50 rounded-xl overflow-hidden flex-shrink-0 relative">
                                        @if($product->main_image)
                                            <img src="{{ storage_url($product->main_image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-surface-200">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-surface-900 group-hover:text-primary-600 transition-colors">{{ $product->name }}</h4>
                                        <p class="text-[10px] font-bold text-surface-400 mt-1">
                                            #{{ $product->barcode ?? $product->sku }} | 
                                            @php
                                                $stock = $product->warehouseStock->first()?->quantity ?? 0;
                                                $stockColor = $stock > 10 ? 'text-success-600' : ($stock > 0 ? 'text-warning-600' : 'text-red-500');
                                            @endphp
                                            <span class="{{ $stockColor }}">{{ __('المخزون:') }} {{ $stock }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-left flex items-center gap-4">
                                    <div class="text-[10px] font-bold text-surface-400 text-right">
                                        <div>{{ __('التكلفة:') }} {{ number_format($product->current_cost_price, 2) }}</div>
                                        @php
                                            $priceData = app(\App\Services\PricingService::class)->safeCalculate($product->purchase_price ?? $product->current_cost_price ?? 0, 'cash');
                                            $cashSellingPrice = $priceData['final_price'] ?? 0;
                                            $profit = $cashSellingPrice - ($product->current_cost_price ?? 0);
                                        @endphp
                                        <div class="{{ $profit > 0 ? 'text-success-600' : 'text-red-500' }}">{{ __('الربح:') }} {{ number_format($profit, 2) }}</div>
                                    </div>
                                    <span class="text-sm font-black text-primary-600 bg-primary-50 px-3 py-1.5 rounded-lg border border-primary-100">
                                        {{ number_format($mode === 'sale' ? $cashSellingPrice : $product->current_cost_price, 2) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-20 text-center">
                                <h3 class="text-lg font-bold text-surface-900">{{ __('لم يتم العثور على منتجات') }}</h3>
                            </div>
                        @endforelse
                    </div>
                @endif

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            </div>

            {{-- Left: Cart Sidebar (Wider Cart -> 7 cols) --}}
            <div class="col-span-12 lg:col-span-7 sticky top-6 space-y-6">
                {{-- Transaction Info --}}
                <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white/50 shadow-2xl shadow-primary-500/5 ring-1 ring-surface-200/50 overflow-hidden relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-white/10 pointer-events-none"></div>
                    <div class="relative px-6 py-5 border-b border-surface-100/80 flex items-center justify-between bg-white/50">
                        <span class="text-sm font-black text-surface-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $mode === 'sale' ? 'bg-primary-500' : 'bg-success-500' }} animate-pulse"></span>
                            {{ __('تفاصيل الفاتورة') }}
                        </span>
                        <div class="px-3 py-1 bg-white border border-surface-200 shadow-sm rounded-lg text-xs font-black {{ $mode === 'sale' ? 'text-primary-600' : 'text-success-600' }}">
                            {{ $mode === 'sale' ? __('بيع') : __('شراء') }}
                        </div>
                    </div>

                    <div class="relative p-6 space-y-5">
                        @if($mode === 'sale')
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2 space-y-1.5 focus-within:relative z-10">
                                    <label class="text-[11px] font-black tracking-wide text-surface-500 pr-1 uppercase">{{ __('اختر العميل') }}</label>
                                    <select wire:model.live="customer_id" class="w-full bg-white/90 border border-surface-200 shadow-sm rounded-xl px-4 py-3 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all">
                                        <option value="">{{ __('اختر العميل...') }}</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-black tracking-wide text-surface-500 pr-1 uppercase">{{ __('طريقة البيع') }}</label>
                                    <select wire:model.live="sale_method" class="w-full bg-white/90 border border-surface-200 shadow-sm rounded-xl px-4 py-3 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all">
                                        <option value="cash">{{ __('كاش') }}</option>
                                        <option value="installment">{{ __('تقسيط') }}</option>
                                        <option value="credit">{{ __('آجل') }}</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-black tracking-wide text-surface-500 pr-1 uppercase">{{ __('تاريخ الاستحقاق') }}</label>
                                    <input type="date" wire:model="due_date" class="w-full bg-white/90 border border-surface-200 shadow-sm rounded-xl px-4 py-2.5 text-sm font-bold outline-none transition-all">
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2 space-y-1.5 focus-within:relative z-10">
                                    <label class="text-[11px] font-black tracking-wide text-surface-500 pr-1 uppercase">{{ __('اختر المورد') }}</label>
                                    <select wire:model.live="supplier_id" class="w-full bg-white/90 border border-surface-200 shadow-sm rounded-xl px-4 py-3 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all">
                                        <option value="">{{ __('اختر المورد...') }}</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-2 space-y-1.5">
                                    <label class="text-[11px] font-black tracking-wide text-surface-500 pr-1 uppercase">{{ __('تاريخ الاستحقاق') }}</label>
                                    <input type="date" wire:model="due_date" class="w-full bg-white/90 border border-surface-200 shadow-sm rounded-xl px-4 py-2.5 text-sm font-bold outline-none transition-all">
                                </div>
                            </div>
                        @endif

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black tracking-wide text-surface-500 pr-1 uppercase">{{ __('اختر المستودع') }}</label>
                            <select wire:model.live="warehouse_id" class="w-full bg-white/90 border border-surface-200 shadow-sm rounded-xl px-4 py-3 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all">
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" wire:click="addCustomItem" class="w-full py-3 bg-surface-100 text-surface-700 font-bold rounded-2xl hover:bg-primary-600 hover:text-white transition-all flex items-center justify-center gap-2 text-xs border border-surface-200 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            {{ __('إضافة عنصر مخصص') }}
                        </button>
                    </div>

                    {{-- Cart Items --}}
                    <div class="relative max-h-[250px] overflow-y-auto border-t border-surface-100/50 bg-surface-50/30 divide-y divide-surface-100 custom-scrollbar">
                        @forelse($items as $index => $item)
                            <div class="p-4 hover:bg-white transition-colors group">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1">
                                        @if($item['id'])
                                            <h5 class="text-sm font-extrabold text-surface-900 text-right">{{ $item['name'] }}</h5>
                                        @else
                                            <input type="text" wire:model.live="items.{{ $index }}.name" class="w-full bg-primary-50/50 border border-primary-200 rounded-lg px-2 py-1 text-xs font-bold focus:ring-2 focus:ring-primary-500/20 outline-none text-right" placeholder="اسم منتج مخصص">
                                        @endif
                                        <div class="flex items-center gap-2 mt-2">
                                            <input type="number" step="any" 
                                                wire:change="updateQuantity({{ $index }}, $event.target.value)" 
                                                value="{{ $item['quantity'] }}" 
                                                class="w-16 bg-white border border-surface-200 shadow-sm rounded-lg px-2 py-1.5 text-xs font-bold focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-center transition-all">
                                            <span class="text-[10px] text-surface-400 font-bold">x</span>
                                            <input type="number" step="0.01" 
                                                wire:change="updatePrice({{ $index }}, $event.target.value)" 
                                                value="{{ $item['price'] }}" 
                                                class="w-24 bg-white border border-surface-200 shadow-sm rounded-lg px-2 py-1.5 text-xs font-bold focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-center transition-all">
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end justify-between h-full">
                                        <button wire:click="removeFromCart({{ $index }})" class="p-1.5 text-surface-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        <input type="number" step="0.01" 
                                            wire:change="updateItemTotal({{ $index }}, $event.target.value)" 
                                            value="{{ $item['total'] }}" 
                                            class="text-sm font-black text-surface-900 mt-2 bg-transparent border-none text-left p-0 focus:ring-0 w-24">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-14 text-center text-surface-400 flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-surface-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                </div>
                                <p class="text-sm font-extrabold">{{ __('السلة فارغة') }}</p>
                                <p class="text-xs text-surface-400 mt-1">{{ __('أضف منتجات للبدء بعملية الشراء/البيع') }}</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Financial Summary --}}
                    <div class="relative p-6 bg-gradient-to-t from-surface-50/80 to-transparent border-t border-surface-200/60 space-y-4">
                        <div class="flex items-center justify-between text-xs font-extrabold text-surface-600">
                            <span>{{ __('الإجمالي الفرعي') }}</span>
                            <span class="text-surface-900 font-black text-sm">{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold text-surface-600 gap-4 bg-surface-100/50 p-3 rounded-xl border border-surface-200/50">
                            <span class="font-extrabold">{{ __('الخصم / الشحن') }}</span>
                            <div class="flex items-center gap-2">
                                <input type="number" wire:model.live="discount" placeholder="{{ __('خصم') }}" class="w-20 py-1.5 text-xs font-bold bg-white border border-surface-200 rounded-lg text-center focus:ring-2 focus:ring-primary-500/20 outline-none shadow-sm">
                                <input type="number" wire:model.live="shipping_cost" placeholder="{{ __('شحن') }}" class="w-20 py-1.5 text-xs font-bold bg-white border border-surface-200 rounded-lg text-center focus:ring-2 focus:ring-primary-500/20 outline-none shadow-sm">
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-xs font-extrabold text-surface-600">
                            <span>{{ __('الرصيد السابق') }}</span>
                            <span class="text-red-500 font-black text-sm" dir="ltr">{{ number_format($previous_balance, 2) }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between pt-4 pb-2 border-t-2 border-dashed border-surface-200 mt-2">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-surface-900">{{ __('الإجمالي المستحق') }}</span>
                                <span class="text-[10px] text-surface-400 font-bold bg-surface-100 px-2 py-0.5 rounded-md mt-1 w-fit">{{ __('صافي الفاتورة: ') }} <span dir="ltr">{{ number_format($this->total, 2) }}</span></span>
                            </div>
                            <span class="text-3xl font-black {{ $mode === 'sale' ? 'text-primary-600' : 'text-success-600' }}" dir="ltr">{{ number_format($this->total_due, 2) }}</span>
                        </div>
                        
                        <div class="pt-3 border-t border-surface-200/60 space-y-2">
                            <label class="text-[11px] font-black tracking-widest text-surface-500 uppercase">{{ __('المبلغ المدفوع') }}</label>
                            <input type="number" wire:model.live="paid_amount" class="w-full bg-white border-2 border-surface-200 focus:border-primary-500 rounded-2xl px-4 py-3 text-xl font-black text-center text-primary-600 focus:ring-4 focus:ring-primary-500/10 outline-none shadow-sm transition-all" placeholder="0.00" dir="ltr">
                        </div>
                        
                        {{-- Logistics Toggle --}}
                        <div x-data="{ expanded: false }" class="border border-surface-200/80 rounded-2xl overflow-hidden bg-white shadow-sm mt-4">
                            <button @click="expanded = !expanded" class="w-full px-4 py-3 text-[11px] font-black uppercase tracking-wider text-surface-600 flex items-center justify-between hover:bg-surface-50 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ __('معلومات إضافية') }}
                                </span>
                                <svg class="w-4 h-4 transition-transform text-surface-400" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="expanded" x-transition class="p-4 space-y-4 bg-surface-50/50 border-t border-surface-100">
                                @if($mode === 'sale')
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="text" wire:model="car_number" placeholder="{{ __('رقم السيارة') }}" class="w-full bg-white border border-surface-200 rounded-xl px-3 py-2 text-sm font-bold shadow-sm outline-none focus:ring-2 focus:ring-primary-500/20">
                                        <input type="text" wire:model="driver_name" placeholder="{{ __('اسم السائق') }}" class="w-full bg-white border border-surface-200 rounded-xl px-3 py-2 text-sm font-bold shadow-sm outline-none focus:ring-2 focus:ring-primary-500/20">
                                    </div>
                                @endif
                                <textarea wire:model="notes" placeholder="{{ __('ملاحظات...') }}" rows="2" class="w-full bg-white border border-surface-200 rounded-xl px-3 py-2 text-sm font-bold shadow-sm outline-none resize-none focus:ring-2 focus:ring-primary-500/20"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="relative p-6 grid grid-cols-2 gap-3 bg-surface-50/80 border-t border-surface-200">
                        <button wire:click="processTransaction" 
                            class="col-span-2 py-4 rounded-2xl bg-gradient-to-r {{ $mode === 'sale' ? 'from-primary-600 to-primary-500 shadow-primary-500/30' : 'from-success-600 to-success-500 shadow-success-500/30' }} text-white font-black shadow-lg hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-3">
                            <svg class="w-6 h-6 border-2 border-white/20 rounded-lg p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="tracking-wide text-[15px]">{{ __('حفظ وطباعة') }}</span>
                        </button>
                        <button wire:click="resetFinancials" 
                            class="py-3 rounded-xl border-2 border-surface-200 bg-white text-surface-700 font-extrabold hover:bg-surface-50 hover:border-surface-300 transition-all text-sm shadow-sm active:scale-95">
                            {{ __('إعادة تعيين') }}
                        </button>
                        <button class="py-3 rounded-xl border-2 border-surface-200 bg-white text-surface-700 font-extrabold hover:bg-surface-50 hover:border-surface-300 transition-all text-sm flex items-center justify-center gap-2 shadow-sm active:scale-95">
                            <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            {{ __('مسودة') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- History Section --}}
        <div class="bg-white rounded-3xl border border-surface-200/60 shadow-soft overflow-hidden">
            <div class="p-6 border-b border-surface-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-surface-400 uppercase tracking-wider pr-1">{{ __('من') }}</label>
                        <input type="date" wire:model.live="historyFrom" class="bg-surface-50 border border-surface-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-surface-400 uppercase tracking-wider pr-1">{{ __('إلى') }}</label>
                        <input type="date" wire:model.live="historyTo" class="bg-surface-50 border border-surface-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 outline-none transition-all">
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-surface-400">{{ __('مبيعات اليوم') }}</p>
                        <p class="text-lg font-black text-primary-600">
                            {{ number_format(\App\Models\SaleInvoice::whereDate('invoice_date', today())->sum('total_amount'), 2) }}
                        </p>
                    </div>
                    <div class="w-px h-8 bg-surface-100"></div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-surface-400">{{ __('مشتريات اليوم') }}</p>
                        <p class="text-lg font-black text-success-600">
                            {{ number_format(\App\Models\PurchaseInvoice::whereDate('invoice_date', today())->sum('total_amount'), 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right divide-y divide-surface-100">
                    <thead class="bg-surface-50/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-black text-surface-500 uppercase tracking-widest">{{ __('رقم المرجع') }}</th>
                            <th class="px-6 py-4 text-xs font-black text-surface-500 uppercase tracking-widest">{{ __('نوع العملية') }}</th>
                            <th class="px-6 py-4 text-xs font-black text-surface-500 uppercase tracking-widest">{{ __('الحساب / الجهة') }}</th>
                            <th class="px-6 py-4 text-xs font-black text-surface-500 uppercase tracking-widest">{{ __('الإجمالي') }}</th>
                            <th class="px-6 py-4 text-xs font-black text-surface-500 uppercase tracking-widest">{{ __('المدفوع') }}</th>
                            <th class="px-6 py-4 text-xs font-black text-surface-500 uppercase tracking-widest">{{ __('المتبقي') }}</th>
                            <th class="px-6 py-4 text-xs font-black text-surface-500 uppercase tracking-widest">{{ __('حالة الفاتورة') }}</th>
                            <th class="px-6 py-4 text-xs font-black text-surface-500 uppercase tracking-widest">{{ __('الإجراءات') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-50">
                        @forelse($historyTransactions as $transaction)
                            <tr class="hover:bg-surface-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-surface-900">#{{ $transaction->invoice_number }}</span>
                                        <span class="text-[10px] text-surface-400 font-bold mt-0.5">{{ $transaction->created_at->translatedFormat('l, d/m/Y - h:i A') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $transaction->history_type === 'sale' ? 'bg-primary-50 text-primary-600 border border-primary-100' : 'bg-success-50 text-success-600 border border-success-100' }}">
                                        {{ $transaction->history_type === 'sale' ? __('بيع') : __('شراء') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-surface-700">
                                    {{ $transaction->history_account }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-black {{ $transaction->history_type === 'sale' ? 'text-primary-600' : 'text-success-600' }}" dir="ltr">
                                        {{ number_format($transaction->total_amount, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-surface-700" dir="ltr">
                                        {{ number_format($transaction->paid_amount ?? 0, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        // Calculate exact remaining based on total and paid.
                                        $remaining = $transaction->total_amount - ($transaction->paid_amount ?? 0);
                                    @endphp
                                    <span class="text-sm font-black {{ $remaining > 0 ? 'text-warning-600' : ($remaining < 0 ? 'text-blue-600' : 'text-surface-400') }}" dir="ltr">
                                        {{ number_format($remaining, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $transaction->status === 'completed' ? 'bg-blue-50 text-blue-600' : 'bg-surface-100 text-surface-500' }}">
                                        {{ __($transaction->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <div class="flex items-center gap-2">
                                        <button class="p-2 text-surface-400 hover:text-primary-600 hover:bg-white rounded-xl shadow-sm transition-all" title="{{ __('عرض') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                        <button wire:click="printInvoice('{{ $transaction->id }}', '{{ $transaction->history_type }}')" 
                                            class="p-2 text-surface-400 hover:text-primary-600 hover:bg-white rounded-xl shadow-sm transition-all" title="{{ __('طباعة الفاتورة') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="text-surface-300">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <p class="text-sm font-bold text-surface-400">{{ __('لا توجد عمليات في هذه الفترة') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @script
    <script>
        $wire.on('print-invoice', (event) => {
            const invoiceId = event.invoiceId;
            const type = event.type;
            const url = type === 'sale' 
                ? `/dashboard/pos/print/sale/${invoiceId}?direct=1` 
                : `/dashboard/pos/print/purchase/${invoiceId}?direct=1`;
            
            const printWindow = window.open(url, '_blank', 'width=400,height=600');
            if (printWindow) {
                printWindow.focus();
            }
        });
    </script>
    @endscript
</div>
