<?php

namespace App\Contracts\Services;

/**
 * Interface UserServiceInterface
 *
 * This interface defines the methods for user service operations.
 *
 * @package App\Contracts\Services
 */
interface UserServiceInterface
{
    /**
     * Create a new user.
     *
     * @param array $data The data to create the user with.
     * @return array The created user instance.
     */
    public function create(array $data): array;

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
     * @return array|null The user instance or null if not found.
     */
    public function findById(int $id): array | null;

    /**
     * Update a user by ID.
     *
     * @param int $id The ID of the user.
     * @param array $data The data to update the user with.
     * @return array The updated user instance.
     */
    public function update(int $id, array $data): array;

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
     * @return array The user instance.
     */
    public function findByUsername(string $username): array;

    /**
     * Find a user by email.
     *
     * @param string $email The email of the user.
     * @return array The user instance.
     */
    public function findByEmail(string $email): array;
}
