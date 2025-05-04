<?php

namespace App\DataAccess\Repositories;

use App\DataAccess\Interfaces\UserRepositoryInterface;
use App\Domain\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Find a user by their ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update a user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    /**
     * Find a user by verification token.
     *
     * @param string $token
     * @return User|null
     */
    public function findByVerificationToken(string $token): ?User
    {
        $tokenRecord = DB::table('verification_tokens')
            ->where('token', $token)
            ->where('created_at', '>', now()->subHours(24)) // Token valid for 24 hours
            ->first();

        if (!$tokenRecord) {
            return null;
        }

        return User::find($tokenRecord->user_id);
    }
}
