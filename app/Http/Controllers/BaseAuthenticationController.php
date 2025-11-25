<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\OtpLoginRequest;
use App\Http\Requests\Authentication\OtpRequest;
use App\Http\Requests\Authentication\PasswordLoginRequest;
use App\Http\Requests\Authentication\SignupRequest;
use App\Notifications\LoginNotification;
use App\Services\AuthenticationService;
use App\Traits\ResponderTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

abstract class BaseAuthenticationController extends Controller
{
    use ResponderTrait;

    protected AuthenticationService $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    abstract protected function getModelClass(): string;
    abstract protected function getResourceClass(): string;
    abstract protected function getUserTypeLabel(): string;

    /**
     * Login with OTP
     *
     * @return JsonResponse
     */
    public function loginOtp(OtpLoginRequest $request): JsonResponse
    {
        $user = $this->getModelClass()::where('mobile', $request->mobile)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'mobile' => ['کاربری با این شماره موبایل یافت نشد'],
            ]);
        }

        if (!$this->authService->validateOtp($user, $request->otp)) {
            throw ValidationException::withMessages([
                'otp' => ['رمز یکبار مصرف اشتباه یا منقضی شده است'],
            ]);
        }

        $user->notify(new LoginNotification());
        
        $this->authService->clearOtp($user);

        $token = $this->authService->createToken($user, $request->header('user-agent') ?? 'unknown');

        $resourceClass = $this->getResourceClass();
        $userTypeLabel = $this->getUserTypeLabel();

        return $this->response([
            'token' => $token,
            $userTypeLabel => $resourceClass::make($user),
        ]);
    }

    /**
     * Login with password
     *
     * @return JsonResponse
     */
    public function loginPassword(PasswordLoginRequest $request): JsonResponse
    {
        $user = $this->getModelClass()::where('mobile', $request->mobile)->first();

        if (!$user) {
            return $this->respondForbidden('کاربری با این شماره موبایل یافت نشد');
        }

        if (!$this->authService->validatePassword($user, $request->password)) {
            return $this->respondForbidden('رمز عبور اشتباه است');
        }

        $token = $this->authService->createToken($user, $request->header('user-agent') ?? 'unknown');

        $this->authService->clearOtp($user);

        $resourceClass = $this->getResourceClass();
        $userTypeLabel = $this->getUserTypeLabel();

        return $this->response([
            'token' => $token,
            $userTypeLabel => $resourceClass::make($user),
        ]);
    }

    /**
     * Send OTP
     *
     * @return JsonResponse
     */
    public function sendOtp(OtpRequest $request): JsonResponse
    {
        $user = $this->getModelClass()::where('mobile', $request->mobile)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'mobile' => ['کاربری با این شماره موبایل یافت نشد'],
            ]);
        }

        $this->authService->generateAndSendOtp($user);
        
        return $this->responseSuccessful('OTP has been sent successfully');
    }

    /**
     * Logout
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->responseSuccessful('Logged out successfully');
    }
}
