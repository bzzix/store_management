<?php

namespace App\Livewire\Dashboard\Suppliers;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Supplier;

class SuppliersDataTable extends DataTableComponent
{
    protected $model = Supplier::class;

    #[\Livewire\Attributes\On('refresh-suppliers')]
    public function refreshTable()
    {
        $this->dispatch('refresh-datatable');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc')
            ->setAdditionalSelects(['suppliers.id', 'suppliers.name', 'suppliers.is_active'])
            ->setSearchStatus(true)
            ->setSearchEnabled()
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setOfflineIndicatorEnabled()
            ->setEmptyMessage(__('No results found'))
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
            Column::make(__('ID'), 'id')
                ->sortable()
                ->searchable(),

            Column::make(__('Name'), 'name')
                ->sortable()
                ->searchable()
                ->format(function($value, $row, $column) {
                    return '<strong>' . $value . '</strong>';
                })
                ->html(),

            Column::make(__('Company'), 'company_name')
                ->sortable()
                ->searchable()
                ->collapseOnMobile(),

            Column::make(__('Phone'), 'phone')
                ->sortable()
                ->searchable()
                ->collapseOnMobile(),

            Column::make(__('Credit Limit'), 'credit_limit')
                ->sortable()
                ->format(function($value) {
                    return $value > 0 ? number_format($value, 2) . ' ج.م' : '<span class="text-muted">-</span>';
                })
                ->html()
                ->collapseOnTablet(),

            Column::make(__('Current Balance'), 'current_balance')
                ->sortable()
                ->format(function($value) {
                    $color = $value > 0 ? 'text-danger' : ($value < 0 ? 'text-success' : 'text-muted');
                    return '<span class="' . $color . '">' . number_format($value, 2) . ' ج.م</span>';
                })
                ->html(),

            Column::make(__('Status'), 'is_active')
                ->sortable()
                ->format(function($value, $row, $column) {
                    $color = $value ? 'success' : 'danger';
                    $text = $value ? __('Active') : __('Inactive');
                    return '<span class="status status-' . $color . '"><span class="status-dot"></span> ' . $text . '</span>';
                })
                ->html(),

            Column::make(__('Actions'))
                ->label(function($row, Column $column) {
                    return view('components.action-buttons-supplier', ['row' => $row]);
                })
                ->html(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make(__('Status'))
                ->options([
                    '' => __('All'),
                    '1' => __('Active'),
                    '0' => __('Inactive'),
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value === '1') {
                        $builder->where('suppliers.is_active', true);
                    } elseif ($value === '0') {
                        $builder->where('suppliers.is_active', false);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        return Supplier::query();
    }

    /**
     * فتح نموذج التعديل
     */
    public function editSupplier($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $this->dispatch('open-edit-modal', supplier: clone $supplier);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Supplier not found'));
        }
    }

    /**
     * فتح نموذج حذف
     */
    public function confirmDelete($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $this->dispatch('open-delete-modal', supplierId: $id, supplierName: $supplier->name);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Supplier not found'));
        }
    }
}
