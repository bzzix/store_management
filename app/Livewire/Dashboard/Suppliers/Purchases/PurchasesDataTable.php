<?php

namespace App\Livewire\Dashboard\Suppliers\Purchases;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PurchaseInvoice;
use Carbon\Carbon;

class PurchasesDataTable extends DataTableComponent
{
    protected $model = PurchaseInvoice::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc')
            ->setAdditionalSelects(['purchase_invoices.id as id'])
            ->setSearchDebounce(500)
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setEmptyMessage(__('No invoices found'))
            ->setTableAttributes([
                'class' => 'w-full text-right divide-y divide-surface-100',
            ])
            ->setTheadAttributes([
                'class' => 'bg-surface-50/50',
            ])
            ->setThAttributes(function(Column $column) {
                return [
                    'class' => 'px-6 py-4 text-xs font-bold text-surface-500 uppercase tracking-wider',
                ];
            })
            ->setTbodyAttributes([
                'class' => 'bg-white divide-y divide-surface-50',
            ])
            ->setTrAttributes(function($item) {
                return [
                    'class' => 'hover:bg-surface-50/50 transition-all',
                ];
            })
            ->setTdAttributes(function(Column $column, $row, $index, $rowIndex) {
                return [
                    'class' => 'px-6 py-4 text-sm font-medium text-surface-700 whitespace-nowrap',
                ];
            });
    }

    public function columns(): array
    {
        return [
            Column::make(__('Invoice Number'), 'invoice_number')
                ->searchable()
                ->sortable(),

            Column::make('Supplier ID', 'supplier_id')->hideIf(true),

            Column::make(__('Date'), 'created_at')
                ->sortable()
                ->format(fn($value) => $value->format('Y-m-d H:i')),

            Column::make(__('Supplier'), 'supplier.name')
                ->searchable()
                ->sortable()
                ->format(function($value, $row) {
                    if (!$row->supplier_id) return $value;

                    return '<a href="' . route('dashboard.suppliers.statement', ['supplier' => $row->supplier_id]) . '" 
                               class="text-primary-600 hover:text-primary-800 font-medium hover:underline transition-colors"
                               title="' . __('View Statement') . '"
                               target="_blank">
                                ' . $value . '
                            </a>';
                })
                ->html(),

            Column::make(__('Warehouse'), 'warehouse.name')
                ->sortable(),

            Column::make(__('Previous Balance'), 'previous_balance')
                ->format(fn($value) => number_format($value, 0))
                ->sortable(),

            Column::make(__('Total Amount'), 'total_amount')
                ->format(fn($value) => number_format($value, 0))
                ->sortable(),

            Column::make(__('Paid Amount'), 'paid_amount')
                ->format(fn($value) => number_format($value, 0))
                ->sortable(),

            Column::make(__('Remaining'), 'id')
                ->format(fn($value, $row) => number_format((float)$row->total_amount - (float)$row->paid_amount, 0)),

            Column::make(__('Payment Status'), 'payment_status')
                ->format(function($value, $row) {
                    $colors = [
                        'unpaid' => 'bg-red-100 text-red-700 border border-red-200',
                        'partial' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                        'paid' => 'bg-green-100 text-green-700 border border-green-200',
                    ];
                    $labels = [
                        'unpaid' => __('Unpaid'),
                        'partial' => __('Partial'),
                        'paid' => __('Paid'),
                    ];
                    $class = $colors[$value] ?? 'bg-surface-100 text-surface-600';
                    $label = $labels[$value] ?? $value;
                    return '<span class="px-3 py-1 rounded-full text-xs font-bold '.$class.'">'.$label.'</span>';
                })
                ->html()
                ->sortable(),

            Column::make(__('Actions'))
                ->label(function($row) {
                    return view('components.action-buttons-purchase-invoice', [
                        'row' => $row,
                    ])->render();
                })
                ->html(),
        ];
    }

    public function filters(): array
    {
        return [

            SelectFilter::make(__('Payment Status'), 'payment_status')
                ->options([
                    '' => __('All'),
                    'unpaid' => __('Unpaid'),
                    'partial' => __('Partial'),
                    'paid' => __('Paid'),
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->where('payment_status', $value);
                    }
                }),

            SelectFilter::make(__('Invoice Date (Quick)'), 'quick_date')
                ->options([
                    '' => __('All'),
                    'today' => __('Today'),
                    'yesterday' => __('Yesterday'),
                    'two_days_ago' => __('Two Days Ago'),
                    'this_week' => __('This Week'),
                    'this_month' => __('This Month'),
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value === 'today') {
                        $builder->whereDate('invoice_date', Carbon::today());
                    } elseif ($value === 'yesterday') {
                        $builder->whereDate('invoice_date', Carbon::yesterday());
                    } elseif ($value === 'two_days_ago') {
                        $builder->whereDate('invoice_date', Carbon::today()->subDays(2));
                    } elseif ($value === 'this_week') {
                        $builder->whereBetween('invoice_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    } elseif ($value === 'this_month') {
                        $builder->whereMonth('invoice_date', Carbon::now()->month)->whereYear('invoice_date', Carbon::now()->year);
                    }
                }),

            DateFilter::make(__('Specific Date'), 'invoice_date')
                ->filter(function(Builder $builder, string $value) {
                    $builder->whereDate('invoice_date', $value);
                }),
        ];
    }

    public function builder(): Builder
    {
        return PurchaseInvoice::query()
            ->with(['supplier', 'warehouse']);
    }
}
