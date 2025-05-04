<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /**
     * Handle temporary user registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function temporaryRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $verificationToken = Str::random(60);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_token' => $verificationToken,
        ]);

        $this->sendVerificationEmail($user, $verificationToken);

        return response()->json([
            'message' => 'User temporarily registered successfully. Please check your email to complete registration.',
            'user' => $user
        ], 201);
    }

    /**
     * Handle permanent user registration.
     *
     * @param  string  $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function permanentRegister($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid verification token'], 400);
        }

        $user->email_verified_at = now();
        $user->verification_token = null; // Clear the token after use
        $user->save();

        return response()->json([
            'message' => 'User registration completed successfully.',
            'user' => $user
        ], 200);
    }

    /**
     * Send verification email to the user.
     *
     * @param  \App\Models\User  $user
     * @param  string  $token
     * @return void
     */
    private function sendVerificationEmail(User $user, $token)
    {
        $verificationUrl = url("/api/auth/verify/{$token}");

        $data = [
            'user' => $user,
            'verificationUrl' => $verificationUrl
        ];

        Mail::send('emails.verification', $data, function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject('Please verify your email address');
        });
    }
}
