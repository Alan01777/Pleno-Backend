<?php

namespace App\Services;

use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create(array $data): array
    {
        return $this->userRepository->create($data)->toArray();
    }

    public function findById(int $id): array | null
    {
        $user = $this->userRepository->findById($id);
        return $user ? $user->toArray() : null;
    }

    public function update(int $id, array $data): array
    {
        $this->userRepository->update($id, $data);
        return $this->userRepository->findById($id)->toArray();
    }

    public function delete(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function findByUsername(string $username): array
    {
        return $this->userRepository->findByUsername($username)->toArray();
    }

    public function findByEmail(string $email): array
    {
        return $this->userRepository->findByEmail($email)->toArray();
    }
}
