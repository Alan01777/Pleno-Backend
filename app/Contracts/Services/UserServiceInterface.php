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
     * @param array $data
     * @return array
     */
    public function create(array $data): array;

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): array | null;

    /**
     * Update a user by ID.
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update(int $id, array $data): array;

    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Find a user by username.
     *
     * @param string $username
     * @return array
     */
    public function findByUsername(string $username): array;

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return array
     */
    public function findByEmail(string $email): array;
}
