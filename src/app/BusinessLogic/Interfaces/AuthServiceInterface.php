<?php

namespace App\BusinessLogic\Interfaces;

use App\Domain\Models\User;

interface AuthServiceInterface
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array;

    /**
     * Verify a user's email using a token.
     *
     * @param string $token
     * @return bool
     */
    public function verifyEmail(string $token): bool;

    /**
     * Authenticate a user and return a JWT token.
     *
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public function login(string $email, string $password): ?array;

    /**
     * Generate a verification token for a user.
     *
     * @param User $user
     * @return string
     */
    public function generateVerificationToken(User $user): string;
}
