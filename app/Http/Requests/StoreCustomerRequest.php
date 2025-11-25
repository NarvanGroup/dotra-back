<?php

namespace App\Http\Requests;

use App\Rules\PersianName;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'national_code' => ['required', 'string', 'size:10'],
            'mobile' => ['required', 'string', 'regex:/^09\\d{9}$/'],
            'first_name' => ['required', 'string', 'max:255', new PersianName],
            'last_name' => ['required', 'string', 'max:255', new PersianName],
            'birth_date' => ['required', 'date'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
