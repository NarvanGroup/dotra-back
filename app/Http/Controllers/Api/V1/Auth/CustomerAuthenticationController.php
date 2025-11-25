<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Requests\Api\V1\Auth\SignupRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CustomerAuthenticationController extends BaseAuthenticationController
{
    protected function getModelClass(): string
    {
        return Customer::class;
    }

    protected function getResourceClass(): string
    {
        return CustomerResource::class;
    }

    protected function getUserTypeLabel(): string
    {
        return 'customer';
    }

    /**
     * Signup
     *
     * @return JsonResponse
     */
    public function register(SignupRequest $request): JsonResponse
    {
        $modelClass = $this->getModelClass();

        if ($modelClass::where('mobile', $request->mobile)->first()) {
            throw ValidationException::withMessages([
                'mobile' => ['این شماره موبایل قبلا ثبت نام کرده است'],
            ]);
        }

        $user = $modelClass::create([
            'mobile'        => $request->mobile,
            'national_code' => $request->national_id,
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'birth_date'    => $request->birth_date,
        ]);

        $this->authService->generateAndSendOtp($user);

        return $this->responseSuccessful('Signup successful, OTP sent');
    }
}
