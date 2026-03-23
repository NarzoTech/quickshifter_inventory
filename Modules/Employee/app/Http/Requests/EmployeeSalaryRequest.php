<?php

namespace Modules\Employee\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeSalaryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $currentYear = (int) now()->format('Y');
        return [
            'amount'       => 'required|numeric|min:0.01',
            'type'         => 'required|in:1,2',
            'date'         => 'required|date',
            'payment_type' => 'required|string',
            'account_id'   => 'required',
            'note'         => 'nullable|string',
            'month'        => 'required|string',
            'year'         => 'required|numeric|digits:4|min:2000|max:' . ($currentYear + 1),
            'salary'       => 'required|numeric|min:0',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function messages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
        ];
    }
}
