<?php

namespace App\Livewire\Dashboard\Warehouses;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class WarehousesDataTable extends DataTableComponent
{
    protected $model = Warehouse::class;

    #[\Livewire\Attributes\On('refresh-warehouses')]
    public function refreshTable(): void
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
            Column::make(__('Code'), 'code')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<span class="badge bg-azure">' . e($value) . '</span>')
                ->html(),
            Column::make(__('Address'), 'address')
                ->searchable()
                ->collapseOnMobile(),
            Column::make(__('Main'), 'is_main')
                ->format(fn ($value) => $value
                    ? '<span class="badge bg-green">' . __('Yes') . '</span>'
                    : '<span class="badge bg-secondary">' . __('No') . '</span>')
                ->html(),
            BooleanColumn::make(__('Status'), 'is_active')
                ->sortable(),

            Column::make(__('Actions'))
                ->label(fn ($row) => view('components.action-buttons-warehouse', ['row' => $row])->render())
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
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->where('is_active', $value === '1');
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        return Warehouse::query()
            ->with('manager');
    }

    public function editWarehouse(int $id): void
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $this->dispatch('open-edit-warehouse-modal', warehouse: $warehouse);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Warehouse not found'));
        }
    }

    public function confirmDelete(int $id): void
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $this->dispatch('open-delete-warehouse-modal', warehouseId: $id, warehouseName: $warehouse->name);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Warehouse not found'));
        }
    }

    public function toggleActive(int $id): void
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->update(['is_active' => !$warehouse->is_active]);
            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Warehouse status updated'));
            $this->dispatch('refresh-warehouses');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error updating warehouse'));
        }
    }
}
