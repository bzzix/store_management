<?php

namespace App\Livewire\Dashboard\Customers;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class CustomersDataTable extends DataTableComponent
{
    protected $model = Customer::class;

    #[\Livewire\Attributes\On('refresh-customers')]
    public function refreshTable()
    {
        $this->dispatch('refresh-datatable');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'desc')
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
            })
            ->setQueryStringStatus(false);
    }

    public function columns(): array
    {
        return [
            Column::make(__('ID'), 'id')
                ->sortable()
                ->searchable(),
            Column::make(__('Name'), 'name')
                ->sortable()
                ->searchable(),
            Column::make(__('Phone'), 'phone')
                ->sortable()
                ->searchable(),
            Column::make(__('Total Sales'), 'total_invoices')
                ->format(fn($value) => number_format($value, 2))
                ->sortable(),
            Column::make(__('Total Paid'), 'total_paid')
                ->format(fn($value) => number_format($value, 2))
                ->sortable(),
            Column::make(__('Type'), 'customer_type')
                ->format(fn($value) => $value === 'company' ? __('Company') : __('Individual'))
                ->sortable(),
            Column::make(__('Opening Balance'), 'opening_balance')
                ->format(fn($value) => number_format($value, 2) . ' ' . __('SAR'))
                ->sortable(),
            Column::make(__('Current Balance'), 'current_balance')
                ->format(fn($value) => number_format($value, 2) . ' ' . __('SAR'))
                ->sortable(),
            BooleanColumn::make(__('Status'), 'is_active')
                ->sortable(),
            Column::make(__('Actions'))
                ->label(function($row) {
                    return view('components.customer-actions', [
                        'row' => $row,
                    ])->render();
                })
                ->html(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make(__('Status'), 'is_active')
                ->options([
                    '' => __('All'),
                    '1' => __('Active'),
                    '0' => __('Inactive'),
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->where('is_active', $value === '1');
                    }
                }),
            SelectFilter::make(__('Type'), 'customer_type')
                ->options([
                    '' => __('All'),
                    'individual' => __('Individual'),
                    'company' => __('Company'),
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->where('customer_type', $value);
                    }
                }),
            SelectFilter::make(__('Balance Status'), 'balance_status')
                ->options([
                    '' => __('All'),
                    'in_debt' => __('In Debt'),
                    'clean' => __('No Debt'),
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value === 'in_debt') {
                        $builder->where('current_balance', '>', 0);
                    } elseif ($value === 'clean') {
                        $builder->where('current_balance', '<=', 0);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        return Customer::query();
    }

    public function editCustomer($id)
    {
        abort_if(!auth()->user()->can('customers_update'), 403);
        $this->dispatch('edit-customer-form', $id);
    }

    public function toggleStatus($id, \App\Services\CustomerService $service)
    {
        abort_if(!auth()->user()->can('customers_update'), 403);
        $customer = Customer::findOrFail($id);
        $service->toggleStatus($customer);
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => __('Success'),
            'msg' => __('Status updated successfully')
        ]);
    }

    public function confirmDelete($id)
    {
        abort_if(!auth()->user()->can('customers_delete'), 403);
        $customer = Customer::findOrFail($id);
        $this->dispatch('delete-customer', ['customerId' => $id, 'customerName' => $customer->name]);
    }
}
