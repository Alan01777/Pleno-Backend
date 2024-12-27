<?php

namespace App\Contracts\Services;

interface UserServiceInterface
{
    # CRUD operations
    public function create(array $data): array;
    public function findById(int $id): array | null;
    public function update(int $id, array $data): array;
    public function delete(int $id): bool;

    # Additional methods
    public function findByUsername(string $username): array;
    public function findByEmail(string $email): array;
}
