<?php
namespace Modules\Expense\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'date'                => 'required|date',
            'amount'              => 'required|numeric|min:0.01',
            'payment_type'        => 'required|array|min:1',
            'payment_type.*'      => 'required|string',
            'account_id'          => 'required|array|min:1',
            'paying_amount'       => 'nullable|array',
            'paying_amount.*'     => 'nullable|numeric|min:0',
            'expense_type_id'     => 'required|exists:expense_types,id',
            'sub_expense_type_id' => 'nullable',
            'expense_supplier_id' => 'nullable|exists:expense_suppliers,id',
            'note'                => 'nullable|string',
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
            'date.required'            => 'Date is required',
            'amount.required'          => 'Amount is required',
            'payment_type.required'    => 'Payment type is required',
            'account_id.required'      => 'Account is required',
            'expense_type_id.required' => 'Expense type is required',
        ];
    }
}
