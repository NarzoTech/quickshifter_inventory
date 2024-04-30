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
            'brand_id' => 'required',
            'slug' => 'required',
            'image' => 'nullable',
            'images' => 'nullable',
            'badge' => 'nullable',
            'price' => 'required',
            'discount' => 'nullable',
            'discount_type' => 'required_if:discount,!=,null',
            'quantity' => 'required|numeric',
            'sku' => 'required',
            'status' => 'required',
            'is_featured' => 'required',
            'is_bestseller' => 'required',
            'is_new' => 'required',
            'is_warranty' => 'required',
            'warranty_duration' => 'required_if:is_warranty,=,1',
            'lang_code' => 'nullable',
            'name' => 'required',
            'short_description' => 'nullable',
            'description' => 'required',
            'additional_information' => 'nullable',
            'tags' => 'required',
            'meta_title' => 'nullable',
            'meta_keywords' => 'nullable',
            'meta_description' => 'nullable',
        ];
    }
}
