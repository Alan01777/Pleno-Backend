<?php

namespace App\Contracts\Services;

use App\Models\File;
use Illuminate\Http\UploadedFile;

interface FileServiceInterface
{
    public function create(UploadedFile $file, int $companyId): File;
    public function update(int $id, UploadedFile $file, int $companyId): bool;

    /**
     * Summary of findById
     * @param int $id The Id of the file to return
     * @return string The download URL of the file in the bucket
     */
    public function findById(int $id): string;

    public function findAllByUserId(): array;

    public function delete(int $id): bool;
}
