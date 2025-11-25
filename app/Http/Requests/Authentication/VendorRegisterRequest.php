<?php

namespace App\Http\Requests\Authentication;

use App\Models\Vendor\Industry;
use App\Models\Vendor\VendorType;
use App\Rules\PersianName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendorRegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', new PersianName()],
            'mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/', 'unique:vendors,mobile'],
            'national_code' => ['required', 'string', 'size:10', 'unique:vendors,national_code'],
            'type' => ['required', 'string', Rule::enum(VendorType::class)],
            'industry' => ['required', 'string', Rule::enum(Industry::class)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'regex:/^0[0-9]{10}$/'],
            'business_license_code' => ['nullable', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'reffered_from' => ['nullable', 'string', 'max:255'],
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
            'name.required' => 'نام فروشگاه الزامی است',
            'name.max' => 'نام فروشگاه نباید بیشتر از ۲۵۵ کاراکتر باشد',
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'فرمت شماره موبایل نامعتبر است (مثال: 09123456789)',
            'mobile.unique' => 'این شماره موبایل قبلا ثبت شده است',
            'national_code.required' => 'کد ملی/شناسه ملی الزامی است',
            'national_code.size' => 'کد ملی/شناسه ملی باید ۱۰ رقم باشد',
            'national_code.unique' => 'این کد ملی/شناسه ملی قبلا ثبت شده است',
            'type.required' => 'نوع فروشنده الزامی است',
            'type.enum' => 'نوع فروشنده انتخاب شده نامعتبر است',
            'industry.required' => 'حوزه فعالیت الزامی است',
            'industry.enum' => 'حوزه فعالیت انتخاب شده نامعتبر است',
            'email.email' => 'فرمت ایمیل نامعتبر است',
            'email.max' => 'ایمیل نباید بیشتر از ۲۵۵ کاراکتر باشد',
            'phone_number.regex' => 'فرمت شماره تلفن نامعتبر است (مثال: 02112345678)',
            'business_license_code.max' => 'کد جواز کسب نباید بیشتر از ۲۵۵ کاراکتر باشد',
            'website_url.url' => 'فرمت آدرس وبسایت نامعتبر است',
            'website_url.max' => 'آدرس وبسایت نباید بیشتر از ۲۵۵ کاراکتر باشد',
            'reffered_from.max' => 'منبع ارجاع نباید بیشتر از ۲۵۵ کاراکتر باشد',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'نام فروشگاه',
            'mobile' => 'شماره موبایل',
            'national_code' => 'کد ملی/شناسه ملی',
            'type' => 'نوع فروشنده',
            'industry' => 'حوزه فعالیت',
            'email' => 'ایمیل',
            'phone_number' => 'شماره تلفن',
            'business_license_code' => 'کد جواز کسب',
            'website_url' => 'آدرس وبسایت',
            'reffered_from' => 'منبع ارجاع',
        ];
    }
}

