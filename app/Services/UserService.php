<?php

namespace App\Services;

use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;

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
     * Create a new user.
     *
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        return $this->userRepository->create($data)->toArray();
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): array | null
    {
        $user = $this->userRepository->findById($id);
        return $user ? $user->toArray() : null;
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
        $this->userRepository->update($id, $data);
        return $this->userRepository->findById($id)->toArray();
    }

    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    /**
     * Find a user by username.
     *
     * @param string $username
     * @return array
     */
    public function findByUsername(string $username): array
    {
        return $this->userRepository->findByUsername($username)->toArray();
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return array
     */
    public function findByEmail(string $email): array
    {
        return $this->userRepository->findByEmail($email)->toArray();
    }
}
