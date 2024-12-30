<?php

namespace App\Contracts\Repositories;

use App\Models\Company;

interface CompanyRepositoryInterface
{
    public function create(array $data): Company;

    public function findById(int $id): ?Company;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function findByName(string $name): ?Company;

    public function findByEmail(string $email): ?Company;
}
