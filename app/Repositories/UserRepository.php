<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

/**
 * Class UserRepository
 *
 * This class implements the UserRepositoryInterface and handles the user-related database operations.
 *
 * @package App\Repositories
 */
class UserRepository implements UserRepositoryInterface
{
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
     * Find all users.
     *
     * @return array
     */
    public function findAll(): array
    {
        return User::where('id', '>', 0)->get()->toArray();
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::where('id', $id)->first();
    }

    /**
     * Update a user by ID.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $user = User::where('id', $id)->first();
        if ($user) {
            return $user->update($data);
        }
        return false;
    }

    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $user = User::where('id', $id)->first();
        if ($user) {
            return $user->delete();
        }
        return false;
    }

    /**
     * Find a user by username.
     *
     * @param string $username
     * @return User|null
     */
    public function findByUsername(string $username): ?User
    {
        return User::where('name', $username)->first();
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Delete all tokens for a user.
     *
     * @param User $user
     * @return void
     */
    public function deleteTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}
