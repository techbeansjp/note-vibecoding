<?php

namespace App\Services\Auth;

use App\DTO\LoginCredentialsDTO;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    /**
     * AuthenticationService constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticate a user and return a JWT token.
     *
     * @param LoginCredentialsDTO $credentials
     * @return array|null
     */
    public function login(LoginCredentialsDTO $credentials): ?array
    {
        $user = $this->userRepository->findByEmail($credentials->email);

        if (!$user || !Hash::check($credentials->password, $user->password)) {
            return null;
        }

        $token = auth()->login($user);

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
