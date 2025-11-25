<?php

namespace App\Http\Requests\Api\V1\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
        $customer = $this->route('customer');

        return [
            'national_code' => [
                'sometimes',
                'string',
                'size:10',
                Rule::unique('customers', 'national_code')->ignore($customer, 'id'),
            ],
            'mobile'        => [
                'sometimes',
                'string',
                'regex:/^09\\d{9}$/',
                Rule::unique('customers', 'mobile')->ignore($customer, 'id'),
            ],
            'first_name'    => ['sometimes', 'string', 'max:255'],
            'last_name'     => ['sometimes', 'string', 'max:255'],
            'birth_date'    => ['sometimes', 'date'],
            'email'         => ['sometimes', 'nullable', 'email', 'max:255'],
            'address'       => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
