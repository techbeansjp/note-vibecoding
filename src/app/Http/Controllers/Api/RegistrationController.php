<?php

namespace App\Http\Controllers\Api;

use App\DTO\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    /**
     * @var RegistrationService
     */
    private RegistrationService $registrationService;

    /**
     * RegistrationController constructor.
     *
     * @param RegistrationService $registrationService
     */
    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $userDTO = new UserDTO($request->validated());
            $result = $this->registrationService->register($userDTO);

            return response()->json([
                'message' => $result['message'],
                'token' => $result['token'],
                'user' => new UserResource($result['user'])
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Registration failed',
                'errors' => ['general' => ['An error occurred during registration']],
                'code' => 'REGISTRATION_ERROR'
            ], 500);
        }
    }

    /**
     * Verify a user's email.
     *
     * @param string $token
     * @return JsonResponse
     */
    public function verify(string $token): JsonResponse
    {
        try {
            $verified = $this->registrationService->verifyEmail($token);

            if (!$verified) {
                return response()->json([
                    'message' => 'Invalid or expired verification token',
                    'errors' => ['token' => ['The verification token is invalid or has expired']],
                    'code' => 'INVALID_TOKEN'
                ], 400);
            }

            return response()->json([
                'message' => 'Email verified successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Verification error: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Verification failed',
                'errors' => ['general' => ['An error occurred during verification']],
                'code' => 'VERIFICATION_ERROR'
            ], 500);
        }
    }
}
