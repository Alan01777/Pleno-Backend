<?php

namespace App\Services;

use App\Contracts\Services\FileServiceInterface;
use App\Contracts\Repositories\FileRepositoryInterface;
use App\Contracts\Services\CompanyServiceInterface;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

/**
 * This service class is responsible for handling file operations.
 *
 * @package App\Services
 */
class FileService implements FileServiceInterface
{
    protected $fileRepository;
    protected $companyService;

    /**
     * FileService constructor.
     *
     * @param FileRepositoryInterface $fileRepository
     * @param CompanyServiceInterface $companyService
     */
    public function __construct(FileRepositoryInterface $fileRepository, CompanyServiceInterface $companyService)
    {
        $this->fileRepository = $fileRepository;
        $this->companyService = $companyService;
    }

    /**
     * Create a new file in the database and upload it to the storage.
     *
     * @param UploadedFile $file
     * @param int $companyId
     * @return File
     */
    public function create(UploadedFile $file, int $companyId): File
    {
        $hashName = $file->hashName();
        $path = Storage::putFileAs('files', $file, $hashName);
        $data = $this->extractFileMetadata($file, $hashName, $path, $companyId);
        return $this->fileRepository->create($data);
    }

    /**
     * Update an existing file.
     *
     * @param int $id
     * @param UploadedFile $file
     * @param int $companyId
     * @return bool
     */
    public function update(int $id, UploadedFile $file, int $companyId): bool
    {
        $existingFile = $this->fileRepository->findById($id);
        Storage::delete($existingFile->path);
        $hashName = $file->hashName();
        $path = Storage::putFileAs('files', $file, $hashName);
        $data = $this->extractFileMetadata($file, $hashName, $path, $companyId);
        return $this->fileRepository->update($id, $data);
    }

    /**
     * Find all files by user ID.
     *
     * @return array
     */
    public function findAllByUserId(): array
    {
        $companies = $this->companyService->findAllByUserId();
        $ids = array_column($companies, 'id');
        $files = $this->fileRepository->findAllByUserId($ids);
        $urls = array_map(function ($file) {
            return [
                'name' => $file['name'],
                'id' => $file['id'],
                'url' => Storage::url($file['path'])
            ];
        }, $files);
        return $urls;
    }

    /**
     * Find a file by ID.
     *
     * @param int $id
     * @return string
     */
    public function findById(int $id): string
    {
        $file = $this->fileRepository->findById($id);
        return Storage::url($file->path);
    }

    /**
     * Delete a file by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $file = $this->fileRepository->findById($id);
        Storage::delete($file->path);
        return $this->fileRepository->delete($id);
    }

    /**
     * Extract metadata from the file.
     *
     * @param UploadedFile $file
     * @param string $hashName
     * @param string $path
     * @param int $companyId
     * @return array
     */
    private function extractFileMetadata(UploadedFile $file, string $hashName, string $path, int $companyId): array
    {
        return [
            'name' => $file->getClientOriginalName(),
            'hash_name' => $hashName,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'user_id' => Auth::id(),
            'company_id' => $companyId,
        ];
    }
}
