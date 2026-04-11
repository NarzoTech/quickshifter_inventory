<?php

namespace Modules\Employee\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalaryIncrementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_ids'   => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'increment_type' => 'required|in:amount,percentage',
            'increment_value' => 'required|numeric|min:0.01',
            'note'           => 'nullable|string|max:255',
        ];
    }
}
