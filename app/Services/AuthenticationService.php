<?php

namespace App\Services;

use App\Notifications\OtpNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    /**
     * OTP expiration time in minutes
     */
    const OTP_EXPIRATION_MINUTES = 5;

    /**
     * Token expiration time in days
     */
    const TOKEN_EXPIRATION_DAYS = 7;

    /**
     * Generate and send OTP to user
     *
     * @param Model $user
     * @return string The generated OTP (for testing purposes)
     */
    public function generateAndSendOtp(Model $user): string
    {
        $otp = random_int(10000, 99999);
        
        $user->update([
            'otp' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(self::OTP_EXPIRATION_MINUTES),
        ]);

        $user->notifyNow(new OtpNotification($otp));

        return (string) $otp;
    }

    /**
     * Validate OTP for user
     *
     * @param Model $user
     * @param string $otp
     * @return bool
     */
    public function validateOtp(Model $user, string $otp): bool
    {
        // Check if OTP exists
        if ($user->otp === null) {
            return false;
        }

        // Check if OTP has expired
        if ($user->otp_expires_at && Carbon::parse($user->otp_expires_at)->isPast()) {
            return false;
        }

        // Validate OTP
        return Hash::check($otp, $user->otp);
    }

    /**
     * Validate password for user
     *
     * @param Model $user
     * @param string $password
     * @return bool
     */
    public function validatePassword(Model $user, string $password): bool
    {
        if ($user->password === null) {
            return false;
        }

        return Hash::check($password, $user->password);
    }

    /**
     * Create authentication token for user
     *
     * @param Model $user
     * @param string $userAgent
     * @return string
     */
    public function createToken(Model $user, string $userAgent): string
    {
        $expiresAt = now()->addDays(self::TOKEN_EXPIRATION_DAYS)->toDateTime();
        
        return $user->createToken(
            $userAgent,
            ['*'],
            $expiresAt
        )->plainTextToken;
    }

    /**
     * Clear OTP from user record
     *
     * @param Model $user
     * @return void
     */
    public function clearOtp(Model $user): void
    {
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);
    }


}
