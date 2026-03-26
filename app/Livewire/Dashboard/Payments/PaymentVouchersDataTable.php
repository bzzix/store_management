<?php

namespace App\Livewire\Dashboard\Payments;

use App\Models\Payment;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Illuminate\Database\Eloquent\Builder;

class PaymentVouchersDataTable extends DataTableComponent
{
    protected $model = Payment::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('payment_date', 'desc')
            ->setTableAttributes(['class' => 'w-full text-right'])
            ->setEmptyMessage(__('No vouchers found'));
    }

    public function columns(): array
    {
        return [
            Column::make(__('Number'), 'payment_number')
                ->sortable()
                ->searchable(),
            
            Column::make(__('Date'), 'payment_date')
                ->sortable(),

            Column::make(__('Type'), 'voucher_type')
                ->format(fn($value) => $value === 'receipt' ? '<span class="text-success font-bold">'.__('Receipt').'</span>' : '<span class="text-primary font-bold">'.__('Disbursement').'</span>')
                ->html()
                ->sortable(),

            Column::make(__('Target'), 'id') // Use 'id' for the data-field to avoid join issues
                ->format(fn($value, $row) => $row->payer ? $row->payer->name : '-')
                ->searchable(function(Builder $query, $searchTerm) {
                    $query->whereHasMorph('payer', [\App\Models\Customer::class, \App\Models\Supplier::class], function($query) use ($searchTerm) {
                        $query->where('name', 'like', '%'.$searchTerm.'%');
                    });
                }),

            Column::make(__('Amount'), 'amount')
                ->format(fn($value) => number_format($value, 2) . ' ج.م')
                ->sortable(),

            Column::make(__('Method'), 'payment_method')
                ->format(fn($value) => __($value))
                ->sortable(),

            Column::make(__('Actions'))
                ->label(function($row) {
                    $printUrl = route('dashboard.payments.print', $row);
                    return '
                        <div class="flex items-center gap-2 justify-end">
                            <a href="'.$printUrl.'" target="_blank" class="p-2 text-surface-500 hover:text-primary-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </a>
                            <button wire:click="$parent.confirmDelete('.$row->id.')" class="p-2 text-surface-500 hover:text-danger-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    ';
                })
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        return Payment::query()->whereNotNull('payments.voucher_type');
    }

    public function filters(): array
    {
        return [
            SelectFilter::make(__('Type'), 'voucher_type')
                ->options([
                    '' => __('All'),
                    'receipt' => __('Receipt'),
                    'disbursement' => __('Disbursement'),
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value === 'receipt') {
                        $builder->where('payments.voucher_type', 'receipt');
                    } elseif ($value === 'disbursement') {
                        $builder->where('payments.voucher_type', 'disbursement');
                    }
                }),
            DateFilter::make(__('Date From'), 'date_from')
                ->filter(fn(Builder $builder, string $value) => $builder->whereDate('payments.payment_date', '>=', $value)),
            DateFilter::make(__('Date To'), 'date_to')
                ->filter(fn(Builder $builder, string $value) => $builder->whereDate('payments.payment_date', '<=', $value)),
        ];
    }
}
