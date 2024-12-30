<?php

namespace App\Contracts\Repositories;

use App\Models\User;

/**
 * Interface UserRepositoryInterface
 *
 * This interface defines the methods for user repository operations.
 *
 * @package App\Contracts\Repositories
 */
interface UserRepositoryInterface
{
    /**
     * Create a new user.
     *
     * @param array $data The data to create the user with.
     * @return User The created user instance.
     */
    public function create(array $data): User;

    /**
     * Find all users.
     *
     * @return array The array of user instances.
     */
    public function findAll(): array;

    /**
     * Find a user by ID.
     *
     * @param int $id The ID of the user.
     * @return User|null The user instance or null if not found.
     */
    public function findById(int $id): ?User;

    /**
     * Update a user by ID.
     *
     * @param int $id The ID of the user.
     * @param array $data The data to update the user with.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a user by ID.
     *
     * @param int $id The ID of the user.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function delete(int $id): bool;

    /**
     * Find a user by username.
     *
     * @param string $username The username of the user.
     * @return User|null The user instance or null if not found.
     */
    public function findByUsername(string $username): ?User;

    /**
     * Find a user by email.
     *
     * @param string $email The email of the user.
     * @return User|null The user instance or null if not found.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Delete all tokens for a user.
     *
     * @param User $user The user to delete tokens for.
     * @return void The user instance or null if not found.
     */
    public function deleteTokens(User $user): void;
}
