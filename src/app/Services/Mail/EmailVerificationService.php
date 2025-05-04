<?php

namespace App\Services\Mail;

use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmailVerificationService
{
    /**
     * Send verification email to the user.
     *
     * @param User $user
     * @return string
     */
    public function sendVerificationEmail(User $user): string
    {
        $token = Str::random(60);

        EmailVerification::create([
            'user_id' => $user->id,
            'token' => $token,
        ]);

        Log::info("Verification token for user {$user->email}: {$token}");
        

        return $token;
    }

    /**
     * Delete a verification token.
     *
     * @param string $token
     * @return bool
     */
    public function deleteVerificationToken(string $token): bool
    {
        return (bool) EmailVerification::where('token', $token)->delete();
    }
}
