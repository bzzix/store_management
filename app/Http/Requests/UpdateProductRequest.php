<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'warehouse_id' => ['sometimes', 'required', 'exists:warehouses,id'],
            'profit_margin_tier_id' => ['nullable', 'exists:profit_margin_tiers,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $productId],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku,' . $productId],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode,' . $productId],
            'description' => ['nullable', 'string'],
            'base_unit' => ['sometimes', 'required', 'string', 'max:50'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'max_stock' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'التصنيف مطلوب',
            'category_id.exists' => 'التصنيف المحدد غير موجود',
            'warehouse_id.required' => 'المخزن مطلوب',
            'warehouse_id.exists' => 'المخزن المحدد غير موجود',
            'name.required' => 'اسم المنتج مطلوب',
            'name.max' => 'اسم المنتج يجب ألا يتجاوز 255 حرف',
            'slug.unique' => 'الرابط المختصر مستخدم مسبقاً',
            'sku.unique' => 'رمز المنتج (SKU) مستخدم مسبقاً',
            'barcode.unique' => 'الباركود مستخدم مسبقاً',
            'base_unit.required' => 'الوحدة الأساسية مطلوبة',
        ];
    }
}
