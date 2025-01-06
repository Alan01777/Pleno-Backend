<?php

namespace App\Services;

use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class UserService
 *
 * This class implements the UserServiceInterface and handles the user-related operations.
 *
 * @package App\Services
 */
class UserService implements UserServiceInterface
{
    protected $userRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Find all users.
     *
     * @return array
     */
    public function findAll(): array
    {
        try {
            return $this->userRepository->findAll();
        } catch (Exception $e) {
            Log::error('Failed to retrieve users: ' . $e->getMessage());
            throw new Exception('Failed to retrieve users.');
        }
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            return $this->userRepository->create($data)->toArray();
        } catch (Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            throw new Exception('Failed to create user.');
        }
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): array | null
    {
        try {
            $user = $this->userRepository->findById($id);
            return $user ? $user->toArray() : null;
        } catch (Exception $e) {
            Log::error('Failed to find user: ' . $e->getMessage());
            throw new Exception('Failed to find user.');
        }
    }

    /**
     * Update a user by ID.
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update(int $id, array $data): array
    {
        try {
            $this->userRepository->update($id, $data);
            return $this->userRepository->findById($id)->toArray();
        } catch (Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage());
            throw new Exception('Failed to update user.');
        }
    }

    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            return $this->userRepository->delete($id);
        } catch (Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            throw new Exception('Failed to delete user.');
        }
    }

    /**
     * Find a user by username.
     *
     * @param string $username
     * @return array
     */
    public function findByUsername(string $username): array
    {
        try {
            return $this->userRepository->findByUsername($username)->toArray();
        } catch (Exception $e) {
            Log::error('Failed to find user by username: ' . $e->getMessage());
            throw new Exception('Failed to find user by username.');
        }
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return array
     */
    public function findByEmail(string $email): array
    {
        try {
            return $this->userRepository->findByEmail($email)->toArray();
        } catch (Exception $e) {
            Log::error('Failed to find user by email: ' . $e->getMessage());
            throw new Exception('Failed to find user by email.');
        }
    }
}
