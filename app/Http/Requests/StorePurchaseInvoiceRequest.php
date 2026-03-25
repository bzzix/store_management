<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseInvoiceRequest extends FormRequest
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
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'invoice_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            
            // Items validation
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.unit_id' => ['nullable', 'exists:product_units,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.cost_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'supplier_id.required' => 'المورد مطلوب',
            'supplier_id.exists' => 'المورد المحدد غير موجود',
            'warehouse_id.required' => 'المخزن مطلوب',
            'warehouse_id.exists' => 'المخزن المحدد غير موجود',
            'due_date.after_or_equal' => 'تاريخ الاستحقاق يجب أن يكون بعد أو يساوي تاريخ الفاتورة',
            'items.required' => 'يجب إضافة صنف واحد على الأقل',
            'items.min' => 'يجب إضافة صنف واحد على الأقل',
            'items.*.product_id.required' => 'المنتج مطلوب',
            'items.*.product_id.exists' => 'المنتج المحدد غير موجود',
            'items.*.quantity.required' => 'الكمية مطلوبة',
            'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
            'items.*.cost_price.required' => 'سعر التكلفة مطلوب',
            'items.*.cost_price.min' => 'سعر التكلفة يجب أن يكون أكبر من أو يساوي صفر',
        ];
    }
}
