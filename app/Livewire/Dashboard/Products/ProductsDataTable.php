<?php

namespace App\Livewire\Dashboard\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class ProductsDataTable extends DataTableComponent
{
    protected $model = Product::class;

    #[\Livewire\Attributes\On('refresh-products')]
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
            Column::make(__('SKU'), 'sku')
                ->sortable()
                ->searchable(),
            
            Column::make(__('Stock Status'), 'real_stock')
                ->sortable()
                ->label(function($row) {
                    $qty = (float)$row->real_stock;
                    $colorClass = 'bg-primary-50 text-primary-600'; // Default
                    
                    if ($qty <= 3) {
                        $colorClass = 'bg-red-50 text-red-600 border border-red-100';
                    } elseif ($qty <= 5) {
                        $colorClass = 'bg-yellow-50 text-yellow-600 border border-yellow-100';
                    } else {
                        $colorClass = 'bg-green-50 text-green-600 border border-green-100';
                    }

                    return '<span class="px-3 py-1 rounded-full text-xs font-bold ' . $colorClass . '">' 
                        . number_format($qty, 0) . ' ' . $row->base_unit 
                        . '</span>';
                })
                ->html(),

            Column::make(__('Category'), 'category.name')
                ->sortable(),

            BooleanColumn::make(__('Status'), 'is_active')
                ->sortable(),

            Column::make(__('Actions'))
                ->label(function($row) {
                    return view('components.product-actions', [
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
        return Product::query()
            ->with('category')
            ->withSum('warehouseStock as real_stock', 'quantity');
    }

    /**
     * فتح نموذج التعديل
     */
    public function editProduct($id)
    {
        abort_if(!auth()->user()->can('products_edit'), 403);
        try {
            $product = Product::findOrFail($id);
            $this->dispatch('open-edit-product', ['productId' => $id]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => __('Error'),
                'msg' => __('Product not found')
            ]);
        }
    }

    /**
     * فتح نموذج حذف
     */
    public function confirmDelete($id)
    {
        abort_if(!auth()->user()->can('products_delete'), 403);
        try {
            $product = Product::findOrFail($id);
            $this->dispatch('delete-product', ['productId' => $id, 'productName' => $product->name]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => __('Error'),
                'msg' => __('Product not found')
            ]);
        }
    }

    /**
     * تبديل الحالة
     */
    public function toggleActive($id)
    {
        abort_if(!auth()->user()->can('products_edit'), 403);
        try {
            $product = Product::findOrFail($id);
            $this->dispatch('toggle-product-active', ['productId' => $id]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => __('Error'),
                'msg' => __('Error updating product')
            ]);
        }
    }
}
