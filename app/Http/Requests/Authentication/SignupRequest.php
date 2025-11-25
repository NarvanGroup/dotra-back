<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
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
            'mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
            'national_id' => ['required', 'string', 'size:10'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'فرمت شماره موبایل نامعتبر است',
            'national_id.required' => 'کد ملی الزامی است',
            'national_id.size' => 'کد ملی باید ۱۰ رقم باشد',
            'first_name.required' => 'نام الزامی است',
            'first_name.max' => 'نام نباید بیشتر از ۲۵۵ کاراکتر باشد',
            'last_name.required' => 'نام خانوادگی الزامی است',
            'last_name.max' => 'نام خانوادگی نباید بیشتر از ۲۵۵ کاراکتر باشد',
            'birth_date.required' => 'تاریخ تولد الزامی است',
            'birth_date.date' => 'فرمت تاریخ تولد نامعتبر است',
            'birth_date.before' => 'تاریخ تولد باید قبل از امروز باشد',
        ];
    }
}
