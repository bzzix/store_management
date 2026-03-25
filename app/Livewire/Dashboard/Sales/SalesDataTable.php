<?php

namespace App\Livewire\Dashboard\Sales;

use App\Models\SaleInvoice;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class SalesDataTable extends DataTableComponent
{
    protected $model = SaleInvoice::class;
 
    protected $listeners = ['refresh-sales' => '$refresh', 'refresh-datatable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc')
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

    public function builder(): Builder
    {
        return SaleInvoice::query()->with(['customer', 'user', 'saleMethod']);
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")->hideIf(true),
            Column::make("Customer ID", "customer_id")->hideIf(true),
            
            Column::make(__('Number'), "invoice_number")
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<span class="font-bold text-surface-900">#' . $value . '</span>')
                ->html(),

            Column::make(__('Customer'), "customer.name")
                ->sortable()
                ->searchable()
                ->format(function($value, $row) {
                    if (!$row->customer_id) return $value;

                    return '<a href="' . route('dashboard.customers.statement', ['customer' => $row->customer_id]) . '" 
                               class="text-primary-600 hover:text-primary-800 font-medium hover:underline transition-colors"
                               title="' . __('View Statement') . '"
                               target="_blank">
                                ' . $value . '
                            </a>';
                })
                ->html(),

            Column::make(__('Date'), "created_at")
                ->sortable()
                ->format(fn($value) => $value->format('Y-m-d H:i')),

            Column::make(__('Total'), "total_amount")
                ->sortable()
                ->format(fn($value) => '<span class="font-bold text-primary-600">' . number_format($value, 2) . ' ' . __('EGP') . '</span>')
                ->html(),

            Column::make(__('Paid'), "paid_amount")
                ->sortable()
                ->format(fn($value) => number_format($value, 2)),

            Column::make(__('Status'), "status")
                ->sortable()
                ->format(function($value) {
                    $colors = [
                        'completed' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        'pending'   => 'bg-blue-100 text-blue-700',
                        'draft'     => 'bg-gray-100 text-gray-700',
                    ];
                    $label = __($value);
                    $color = $colors[$value] ?? 'bg-surface-100';
                    return '<span class="px-2 py-1 rounded-lg text-xs font-bold ' . $color . '">' . $label . '</span>';
                })
                ->html(),

            Column::make(__('Actions'))
                ->label(fn($row) => view('components.sale-actions', ['row' => $row])),
        ];
    }

    public function showInvoice($id)
    {
        // For view modal or full page
    }

    public function printInvoice($id)
    {
        // This will be handled via JS/Route
    }
}
