<?php

namespace App\Services\Auth;

use App\DTO\UserDTO;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Mail\EmailVerificationService;
use App\UnitOfWork\UnitOfWorkInterface;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    /**
     * @var EmailVerificationService
     */
    private EmailVerificationService $emailVerificationService;

    /**
     * @var UnitOfWorkInterface
     */
    private UnitOfWorkInterface $unitOfWork;

    /**
     * RegistrationService constructor.
     *
     * @param UserRepositoryInterface $userRepository
     * @param EmailVerificationService $emailVerificationService
     * @param UnitOfWorkInterface $unitOfWork
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        EmailVerificationService $emailVerificationService,
        UnitOfWorkInterface $unitOfWork
    ) {
        $this->userRepository = $userRepository;
        $this->emailVerificationService = $emailVerificationService;
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * Register a new user.
     *
     * @param UserDTO $userDTO
     * @return array
     */
    public function register(UserDTO $userDTO): array
    {
        return $this->unitOfWork->execute(function () use ($userDTO) {
            $userData = $userDTO->toArray();
            $userData['password'] = Hash::make($userData['password']);
            $userData['status'] = 'provisional';

            $user = $this->userRepository->create($userData);

            $token = $this->emailVerificationService->sendVerificationEmail($user);

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
        return $this->unitOfWork->execute(function () use ($token) {
            $user = $this->userRepository->findByVerificationToken($token);

            if (!$user) {
                return false;
            }

            $this->userRepository->update($user, [
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $this->emailVerificationService->deleteVerificationToken($token);

            return true;
        });
    }
}
