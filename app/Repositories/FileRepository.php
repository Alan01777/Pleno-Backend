<?php

namespace App\Repositories;

use App\Contracts\Repositories\FileRepositoryInterface;
use App\Models\File;

class FileRepository implements FileRepositoryInterface
{
    public function create(array $data): File
    {
        return File::create($data);
    }

    public function findById(int $id): ?File
    {
        return File::where('id', $id)->first();
    }

    public function findAllByUserId(array $ids): array
    {
        // return all files associated with the ids in the array
        return File::whereIn('company_id', $ids)->get()->toArray();
    }

    public function update(int $id, array $data): bool
    {
        $file = File::where('id', $id)->first();;
        return $file->update($data);
    }

    public function delete(int $id): bool
    {
        $file = File::where('id', $id)->first();;
        return $file->delete();
    }
}
