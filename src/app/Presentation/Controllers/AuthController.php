<?php

namespace App\Presentation\Controllers;

use App\BusinessLogic\Interfaces\AuthServiceInterface;
use App\Http\Controllers\Controller;
use App\Presentation\Requests\LoginRequest;
use App\Presentation\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * @var AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * AuthController constructor.
     *
     * @param AuthServiceInterface $authService
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
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
            $result = $this->authService->register($request->validated());

            return response()->json([
                'message' => $result['message'],
                'token' => $result['token'],
                'user' => $result['user']
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
            $verified = $this->authService->verifyEmail($token);

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

    /**
     * Login a user.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            $result = $this->authService->login($credentials['email'], $credentials['password']);

            if (!$result) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    'errors' => ['credentials' => ['The provided credentials are incorrect']],
                    'code' => 'INVALID_CREDENTIALS'
                ], 401);
            }

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $result['access_token'],
                'token_type' => $result['token_type'],
                'expires_in' => $result['expires_in'],
                'user' => $result['user']
            ]);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Login failed',
                'errors' => ['general' => ['An error occurred during login']],
                'code' => 'LOGIN_ERROR'
            ], 500);
        }
    }
}
