<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findById(int $id): ?User;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByUsername(string $username): ?User;
    public function findByEmail(string $email): ?User;
}
