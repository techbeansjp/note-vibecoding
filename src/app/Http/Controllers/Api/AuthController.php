<?php

namespace App\Http\Controllers\Api;

use App\DTO\LoginCredentialsDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * @var AuthenticationService
     */
    private AuthenticationService $authService;

    /**
     * AuthController constructor.
     *
     * @param AuthenticationService $authService
     */
    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
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
            $credentials = new LoginCredentialsDTO($request->validated());
            $result = $this->authService->login($credentials);

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
                'user' => new UserResource($result['user'])
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
