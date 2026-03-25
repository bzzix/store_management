<?php

namespace App\Livewire\Dashboard\Pricing;

use App\Models\SaleMethod;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class SaleMethodesDataTable extends DataTableComponent
{
    protected $model = SaleMethod::class;

    #[\Livewire\Attributes\On('refresh-sale-methods')]
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
            Column::make(__('Code'), 'code')
                ->sortable()
                ->searchable(),
            Column::make(__('Priority'), 'priority')
                ->sortable()
                ->format(function($value) {
                    return '<span class="badge bg-blue">' . $value . '</span>';
                })
                ->html(),

            BooleanColumn::make(__('Status'), 'is_active')
                ->sortable(),

            Column::make(__('Actions'))
                ->label(function($row) {
                    return view('components.sale-method-actions', [
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
        ];
    }

    public function builder(): Builder
    {
        return SaleMethod::query();
    }

    /**
     * فتح نموذج التعديل
     */
    public function editMethod($id)
    {
        try {
            $method = SaleMethod::findOrFail($id);
            $this->dispatch('open-edit-method', methodId: $id);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Sale method not found'));
        }
    }

    /**
     * فتح نموذج حذف
     */
    public function confirmDelete($id)
    {
        try {
            $method = SaleMethod::findOrFail($id);
            $this->dispatch('open-delete-method-modal', methodId: $id, methodName: $method->name);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Sale method not found'));
        }
    }

    /**
     * تبديل الحالة
     */
    public function toggleActive($id)
    {
        try {
            $method = SaleMethod::findOrFail($id);
            $this->dispatch('toggle-method-active', methodId: $id);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error updating sale method'));
        }
    }
}
