<?php

namespace App\Livewire\Dashboard\Pricing;

use App\Models\ProfitMarginTier;
use App\Models\ProfitMarginTierMethod;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Columns\EditColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use App\Services\ProfitMarginTierService;

class ProfitMarginDataTable extends DataTableComponent
{
    protected $model = ProfitMarginTierMethod::class;

        public function builder(): Builder
    {
        return ProfitMarginTierMethod::query()
            ->with(['profitMarginTier', 'saleMethod']);
    }

    #[\Livewire\Attributes\On('mount')]
    public function mount()
    {
    }

    #[\Livewire\Attributes\On('refresh-tiers')]
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
            Column::make(__('Tier'), 'profitMarginTier.name')
                ->sortable()
                ->searchable(),
            Column::make(__('Sale Method'), 'saleMethod.name')
                ->sortable()
                ->searchable(),
            Column::make(__('Profit Value'), 'profit_value')
                ->sortable()
                ->format(function($value) {
                    if ($value === null) {
                        return '<span class="badge bg-info text-dark">' . __('Unlimited') . '</span>';
                    }
                    return number_format($value, 2);
                })
                ->html(),
            // Column::make(__('Actions'))
            //     ->label(function($row) {
            //         return view('components.action-buttons', [
            //             'row' => $row,
            //         ])->render();
            //     })
            //     ->html(),
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

    // الإجراءات
    public function editTier($id)
    {
        try {
            $tier = ProfitMarginTierMethod::findOrFail($id);
            $this->dispatch('open-edit-modal', tier: $tier);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Tier not found'));
        }
    }

    public function confirmDelete($id)
    {
        //dd('fffffff');
        try {
            $tier = ProfitMarginTierMethod::findOrFail($id);
            $this->dispatch('open-delete-modal', type: 'question', message: __('Tier updated successfully'), tierId: $id, tierName: $tier->name);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Tier not found'));
        }
    }

    public function updateTier($tierId, array $data)
    {
        try {
            $tier = ProfitMarginTierMethod::findOrFail($tierId);
            $this->tierService->updateTier($tier, $data);
            $this->dispatch('notify', type: 'success', message: __('Tier updated successfully'));
            $this->refresh();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error updating tier: ') . $e->getMessage());
        }
    }

    public function createTier(array $data)
    {
        try {
            $this->tierService->createTier($data);
            $this->dispatch('notify', type: 'success', message: __('Tier created successfully'));
            $this->refresh();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error creating tier: ') . $e->getMessage());
        }
    }

    public function toggleActive($id)
    {
        try {
            $tier = ProfitMarginTierMethod::findOrFail($id);
            $tier->update(['is_active' => !$tier->is_active]);
            $this->dispatch('notify', type: 'success', message: __('Tier status updated'));
            $this->refresh();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error updating tier'));
        }
    }
}