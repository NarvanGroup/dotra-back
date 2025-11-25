<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\VendorRegisterRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VendorAuthenticationController extends BaseAuthenticationController
{
    protected function getModelClass(): string
    {
        return Vendor::class;
    }

    protected function getResourceClass(): string
    {
        return VendorResource::class;
    }

    protected function getUserTypeLabel(): string
    {
        return 'vendor';
    }

    /**
     * Register a new vendor
     *
     * @param VendorRegisterRequest $request
     * @return JsonResponse
     */
    public function register(VendorRegisterRequest $request): JsonResponse
    {
        // Check if vendor already exists with this mobile
        if (Vendor::where('mobile', $request->mobile)->exists()) {
            throw ValidationException::withMessages([
                'mobile' => ['این شماره موبایل قبلا ثبت شده است'],
            ]);
        }

        // Check if vendor already exists with this national_code
        if (Vendor::where('national_code', $request->national_code)->exists()) {
            throw ValidationException::withMessages([
                'national_code' => ['این کد ملی/شناسه ملی قبلا ثبت شده است'],
            ]);
        }

        // Generate unique slug from name
        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;

        while (Vendor::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Create the vendor
        $vendor = Vendor::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'slug' => $slug,
            'national_code' => $request->national_code,
            'type' => $request->type,
            'industry' => $request->industry,
            'owner_first_name' => $request->owner_first_name,
            'owner_last_name' => $request->owner_last_name,
            'owner_birth_date' => $request->owner_birth_date,
            'business_license_code' => $request->business_license_code,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'website_url' => $request->website_url,
            'reffered_from' => $request->reffered_from,
        ]);

        // Generate and send OTP for verification
        $this->authService->generateAndSendOtp($vendor);

        return $this->response([
            'message' => 'ثبت‌نام با موفقیت انجام شد. کد تایید به شماره موبایل شما ارسال شد',
            'vendor' => VendorResource::make($vendor),
        ], 201);
    }
}
