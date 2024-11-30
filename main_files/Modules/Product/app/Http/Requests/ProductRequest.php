<?php

namespace Modules\Product\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required',
            'short_description' => 'nullable',
            'brand_id' => 'nullable',
            'category_id' => 'required',
            'unit_id' => 'required',
            'unit_sale_id' => 'required',
            'unit_purchase_id' => 'required',
            'images' => 'required',
            'cost' => 'nullable',
            'price' => 'nullable',
            'stock_alert' => 'nullable',
            'is_imei' => 'nullable',
            'not_selling' => 'nullable',
            'stock' => 'nullable',
            'stock_status' => 'nullable',
            'sku' => 'required',
            'barcode' => 'required',
            'status' => 'required',
            "tax_type" => 'nullable',
            "tax" => 'nullable',
        ];
    }
}
