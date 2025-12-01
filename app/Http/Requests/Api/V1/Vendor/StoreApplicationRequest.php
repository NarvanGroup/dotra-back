<?php

namespace App\Http\Requests\Api\V1\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $vendor = $this->route('vendor');

        return [
            'customer_id'     => [
                'required',
                'uuid',
                Rule::exists('customers', 'id'),
                Rule::exists('customer_vendor', 'customer_id')
                    ->where(fn($query) => $query->where('vendor_id', $vendor?->id))
            ],
            'credit_score_id' => [
                'required',
                'uuid',
                Rule::exists('credit_scores', 'id')
                    ->where(fn($query) => $query->where('customer_id', $this->input('customer_id')))
            ],
            'principal_amount'    => ['sometimes', 'nullable', 'integer', 'min:0'],
            'down_payment_amount' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    $principalAmount = $this->input('principal_amount');
                    if ($value !== null && $principalAmount !== null && $value > $principalAmount) {
                        $fail('The down payment amount cannot exceed the principal amount.');
                    }
                },
            ],
            'total_payable_amount' => ['prohibited'], // Auto-calculated, cannot be set manually
        ];
    }
}
