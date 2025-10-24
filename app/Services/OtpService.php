<?php

namespace App\Services;

use App\Models\User;
use App\Models\OtpAttempt;
use Carbon\Carbon;

class OtpService
{
    /**
     * Generates a new OTP, invalidates previous unused ones, and saves the new OTP to the database.
     * Uses UTC time for consistency, resolving local time/timezone issues.
     *
     * @param User $user
     * @param string $recipient The phone number or email of the recipient (e.g., 88017XXXXXXXX).
     * @param string $purpose The purpose of the OTP (e.g., 'login').
     * @param int $expiryMinutes The number of minutes until the OTP expires.
     * @return int The generated OTP code.
     */
    public function generateAndSaveOtp(User $user, string $recipient, string $purpose = 'login', int $expiryMinutes = 5): int
    {
        // 1. Invalidate previous unused OTPs using UTC
        OtpAttempt::where('recipient', $recipient)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->where('expires_at', '>', Carbon::now('Asia/Dhaka'))
            ->update(['used_at' => Carbon::now('Asia/Dhaka')]);

        // 2. Generate a 4-digit OTP
        $otpCode = rand(1000, 9999);

        // 3. Save the new OTP using UTC
        OtpAttempt::create([
            'user_id' => $user->id,
            'recipient' => $recipient,
            'otp_code' => $otpCode,
            'expires_at' => Carbon::now('Asia/Dhaka')->addMinutes($expiryMinutes),
            'purpose' => $purpose,
        ]);

        return $otpCode;
    }

    /**
     * Verifies the OTP code.
     *
     * @param string $recipient
     * @param string $otpCode
     * @param string $purpose
     * @return OtpAttempt|null
     */
    public function verifyOtp(string $recipient, string $otpCode, string $purpose = 'login'): ?OtpAttempt
    {
        $otpAttempt = OtpAttempt::where('recipient', $recipient)
            ->where('purpose', $purpose)
            ->where('otp_code', $otpCode)
            ->whereNull('used_at')
            ->where('expires_at', '>', Carbon::now('Asia/Dhaka'))
            ->latest()
            ->first();

        return $otpAttempt;
    }

    /**
     * Marks the OTP as used.
     *
     * @param OtpAttempt $otpAttempt
     * @return bool
     */
    public function markOtpAsUsed(OtpAttempt $otpAttempt): bool
    {
        return $otpAttempt->update(['used_at' => Carbon::now('Asia/Dhaka')]);
    }
}
