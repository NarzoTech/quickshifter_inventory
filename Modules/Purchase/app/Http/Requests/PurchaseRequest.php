<?php

namespace Modules\Purchase\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'supplier_id'      => 'required|exists:supplier,id',
            'purchase_date'    => 'required|date',
            'items'            => 'required',
            'total_amount'     => 'required|numeric|min:0',
            'paid_amount'      => 'required|array|min:1',
            'paid_amount.*'    => 'required|numeric|min:0',
            'due_amount'       => 'required|numeric|min:0',
            'payment_type'     => 'required|array|min:1',
            'payment_type.*'   => 'required|string|in:cash,bank,mobile_banking,card,advance',
            'product_id'       => 'required|array|min:1',
            'product_id.*'     => 'required|distinct|exists:products,id',
            'quantity'         => 'required|array|min:1',
            'quantity.*'       => 'required|numeric|min:0.01',
            'unit_price'       => 'required|array|min:1',
            'unit_price.*'     => 'required|numeric|min:0',
            'selling_price'    => 'required|array|min:1',
            'selling_price.*'  => 'required|numeric|min:0',
            'total'            => 'required|array|min:1',
            'total.*'          => 'required|numeric|min:0',
            'profit'           => 'required|array',
            'profit.*'         => 'required|numeric',
            'stock'            => 'required|array',
            'stock.*'          => 'required|numeric',
        ];
    }

    /**
     * Custom validation after standard rules pass.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $paymentTypes = $this->input('payment_type', []);
            $paidAmounts = $this->input('paid_amount', []);
            $accountIds = $this->input('account_id', []);

            if (!is_array($paymentTypes) || !is_array($paidAmounts)) return;

            // Ensure payment_type and paid_amount arrays have the same count
            if (count($paymentTypes) !== count($paidAmounts)) {
                $validator->errors()->add('payment_type', 'Payment types and amounts must have the same number of entries.');
            }

            // Ensure account_id array has the same count
            if (is_array($accountIds) && count($paymentTypes) !== count($accountIds)) {
                $validator->errors()->add('account_id', 'Account IDs must match the number of payment entries.');
            }

            // Ensure non-cash/non-advance payment types have a valid account_id
            foreach ($paymentTypes as $key => $type) {
                if (!in_array($type, ['cash', 'advance'])) {
                    $accId = $accountIds[$key] ?? null;
                    if (!$accId || !is_numeric($accId)) {
                        $validator->errors()->add("account_id.{$key}", "Account is required for {$type} payment.");
                    }
                }
            }

            // Ensure total paid does not exceed total amount (server recalculates, but warn early)
            $totalPaid = array_sum(array_map('floatval', $paidAmounts));
            $totalAmount = (float) $this->input('total_amount', 0);
            if ($totalPaid > $totalAmount + 0.01 && $totalAmount > 0) {
                $validator->errors()->add('paid_amount', 'Total paid amount cannot exceed the purchase total.');
            }

            // Prevent multiple advance payment rows
            $advanceCount = count(array_filter($paymentTypes, fn($t) => $t === 'advance'));
            if ($advanceCount > 1) {
                $validator->errors()->add('payment_type', 'Only one advance payment entry is allowed.');
            }
        });
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the error messages for the defined validation rules.
     */

    public function messages(): array
    {
        return [
            'supplier_id.required'      => 'Supplier is required',
            'supplier_id.exists'        => 'Selected supplier does not exist',
            'purchase_date.required'    => 'Purchase date is required',
            'items.required'            => 'Items is required',
            'total_amount.required'     => 'Total amount is required',
            'total_amount.numeric'      => 'Total amount must be a number',
            'paid_amount.required'      => 'Paid amount is required',
            'paid_amount.*.numeric'     => 'Each paid amount must be a number',
            'due_amount.required'       => 'Due amount is required',
            'due_amount.numeric'        => 'Due amount must be a number',
            'payment_type.required'     => 'Payment type is required',
            'payment_type.array'        => 'Payment type must be an array',
            'payment_type.*.in'         => 'Invalid payment type selected',
            'product_id.required'       => 'Product is required',
            'product_id.*.required'     => 'Product is required',
            'product_id.*.exists'       => 'Selected product does not exist',
            'quantity.required'         => 'Quantity is required',
            'quantity.*.required'       => 'Quantity is required',
            'quantity.*.min'            => 'Quantity must be at least 0.01',
            'unit_price.required'       => 'Unit price is required',
            'unit_price.*.numeric'      => 'Unit price must be a number',
            'selling_price.required'    => 'Selling price is required',
            'selling_price.*.numeric'   => 'Selling price must be a number',
            'stock.required'            => 'Stock is required',
            'stock.*.required'          => 'Stock is required',
        ];
    }
}
