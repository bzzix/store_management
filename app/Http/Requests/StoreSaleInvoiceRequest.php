<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleInvoiceRequest extends FormRequest
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
            'customer_id' => ['nullable', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'sale_method_id' => ['required', 'exists:sale_methods,id'],
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
            'items.*.sale_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_id.exists' => 'العميل المحدد غير موجود',
            'warehouse_id.required' => 'المخزن مطلوب',
            'warehouse_id.exists' => 'المخزن المحدد غير موجود',
            'sale_method_id.required' => 'طريقة البيع مطلوبة',
            'sale_method_id.exists' => 'طريقة البيع المحددة غير موجودة',
            'due_date.after_or_equal' => 'تاريخ الاستحقاق يجب أن يكون بعد أو يساوي تاريخ الفاتورة',
            'items.required' => 'يجب إضافة صنف واحد على الأقل',
            'items.min' => 'يجب إضافة صنف واحد على الأقل',
            'items.*.product_id.required' => 'المنتج مطلوب',
            'items.*.product_id.exists' => 'المنتج المحدد غير موجود',
            'items.*.quantity.required' => 'الكمية مطلوبة',
            'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
            'items.*.sale_price.required' => 'سعر البيع مطلوب',
            'items.*.sale_price.min' => 'سعر البيع يجب أن يكون أكبر من أو يساوي صفر',
        ];
    }
}
