<?php

namespace App\BusinessLogic\Services;

use App\BusinessLogic\Interfaces\AuthServiceInterface;
use App\DataAccess\Interfaces\UserRepositoryInterface;
use App\Domain\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    /**
     * AuthService constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => 'provisional',
            ]);

            $token = $this->generateVerificationToken($user);

            $this->sendVerificationEmail($user, $token);

            return [
                'user' => $user,
                'token' => $token,
                'message' => 'User registered successfully. Please verify your email.'
            ];
        });
    }

    /**
     * Verify a user's email using a token.
     *
     * @param string $token
     * @return bool
     */
    public function verifyEmail(string $token): bool
    {
        $user = $this->userRepository->findByVerificationToken($token);

        if (!$user) {
            return false;
        }

        $this->userRepository->update($user, [
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        DB::table('verification_tokens')
            ->where('token', $token)
            ->delete();

        return true;
    }

    /**
     * Authenticate a user and return a JWT token.
     *
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public function login(string $email, string $password): ?array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
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

    /**
     * Generate a verification token for a user.
     *
     * @param User $user
     * @return string
     */
    public function generateVerificationToken(User $user): string
    {
        $token = Str::random(60);

        DB::table('verification_tokens')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $token;
    }

    /**
     * Send verification email to the user.
     *
     * @param User $user
     * @param string $token
     * @return void
     */
    private function sendVerificationEmail(User $user, string $token): void
    {
        \Log::info("Verification token for user {$user->email}: {$token}");
        
    }
}
