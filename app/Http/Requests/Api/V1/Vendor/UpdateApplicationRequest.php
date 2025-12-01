<?php

namespace App\Http\Requests\Api\V1\Vendor;

use App\Models\Application\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicationRequest extends FormRequest
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
        $application = $this->route('application');
        $principalAmount = $this->input('principal_amount', $application?->principal_amount);

        return [
            'customer_id'                      => ['sometimes', 'uuid', 'exists:customers,id'],
            'credit_score_id'                  => ['prohibited'],
            'principal_amount'                 => ['sometimes', 'nullable', 'integer', 'min:0'],
            'down_payment_amount'              => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($principalAmount) {
                    if ($value !== null && $principalAmount !== null && $value > $principalAmount) {
                        $fail('The down payment amount cannot exceed the principal amount.');
                    }
                },
            ],
            'total_payable_amount'             => ['prohibited'], // Auto-calculated, cannot be set manually
            'number_of_installments'           => ['sometimes', 'integer', 'between:1,32767'],
            'interest_rate'                    => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'suggested_total_amount'           => ['prohibited'],
            'suggested_number_of_installments' => ['prohibited'],
            'suggested_interest_rate'          => ['prohibited'],
            'status'                           => ['sometimes', 'string', Rule::enum(Status::class)],
        ];
    }
}
