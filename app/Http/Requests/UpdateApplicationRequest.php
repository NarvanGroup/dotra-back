<?php

namespace App\Http\Requests;

use App\Models\Application;
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
        return [
            'customer_id' => ['sometimes', 'uuid', 'exists:customers,uuid'],
            'credit_score_id' => ['prohibited'],
            'total_amount' => ['sometimes', 'integer', 'min:1'],
            'number_of_installments' => ['sometimes', 'integer', 'between:1,32767'],
            'interest_rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'suggested_total_amount' => ['prohibited'],
            'suggested_number_of_installments' => ['prohibited'],
            'suggested_interest_rate' => ['prohibited'],
            'status' => ['sometimes', 'string', Rule::enum(Status::class)],
        ];
    }
}
