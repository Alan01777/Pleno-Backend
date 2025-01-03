<?php

namespace App\Contracts\Repositories;

use App\Models\File;

interface FileRepositoryInterface
{
    public function create(array $data): File;
    public function findById(int $id): ?File;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findAllByUserId(array $ids): array;
}
