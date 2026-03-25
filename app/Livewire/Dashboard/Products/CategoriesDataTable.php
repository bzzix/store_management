<?php

namespace App\Livewire\Dashboard\Products;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class CategoriesDataTable extends DataTableComponent
{
    protected $model = Category::class;

    #[\Livewire\Attributes\On('refresh-categories')]
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
            // Column::make(__('Description'), 'description')
            //     ->sortable()
            //     ->searchable(),
            Column::make(__('Products Count'))
                ->label(function($row) {
                    $count = $row->products()->count();
                    return '<span class="px-2 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-lg border border-blue-100">' . $count . ' ' . trans_choice(__('Product|Products'), $count) . '</span>';
                })
                ->html(),

            BooleanColumn::make(__('Status'), 'is_active')
                ->sortable(),

            Column::make(__('Actions'))
                ->label(function($row) {
                    return view('components.category-actions', [
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
        return Category::query()
            ->withCount('products');
    }

    /**
     * فتح نموذج التعديل
     */
    public function editCategory($id)
    {
        try {
            $category = Category::findOrFail($id);
            $this->dispatch('open-edit-category', categoryId: $id);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Category not found'));
        }
    }

    /**
     * فتح نموذج حذف
     */
    public function confirmDelete($id)
    {
        try {
            $category = Category::findOrFail($id);
            $this->dispatch('delete-category', categoryId: $id, categoryName: $category->name);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Category not found'));
        }
    }

    /**
     * تبديل الحالة
     */
    public function toggleActive($id)
    {
        try {
            $category = Category::findOrFail($id);
            $this->dispatch('toggle-category-active', categoryId: $id);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error updating category'));
        }
    }
}
