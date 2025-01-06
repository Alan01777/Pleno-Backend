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
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

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
     * @throws Exception
     */
    public function create(UploadedFile $file, int $companyId): File
    {
        try {
            $hashName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = Storage::putFileAs('files', $file, $hashName);
            $data = $this->extractFileMetadata($file, $hashName, $path, $companyId);
            return $this->fileRepository->create($data);
        } catch (Exception $e) {
            Log::error('File creation failed: ' . $e->getMessage());
            throw new Exception('File creation failed.');
        }
    }

    /**
     * Update an existing file.
     *
     * @param int $id
     * @param UploadedFile $file
     * @param int $companyId
     * @return bool
     * @throws Exception
     */
    public function update(int $id, UploadedFile $file, int $companyId): bool
    {
        try {
            $existingFile = $this->fileRepository->findById($id);
            if (!$existingFile) {
                throw new NotFoundHttpException('File not found.');
            }
            Storage::delete($existingFile->path);
            $hashName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = Storage::putFileAs('files', $file, $hashName);
            $data = $this->extractFileMetadata($file, $hashName, $path, $companyId);
            return $this->fileRepository->update($id, $data);
        } catch (Exception $e) {
            Log::error('File update failed: ' . $e->getMessage());
            throw new Exception('File update failed.');
        }
    }

    /**
     * Find all files by user ID.
     *
     * @return array
     */
    public function findAllByUserId(): array
    {
        try {
            $companies = $this->companyService->findAllByUserId();
            $ids = array_column($companies, 'id');
            $files = $this->fileRepository->findAllByUserId($ids);

            return array_map(function ($file) {
                return [
                    'id' => $file['id'],
                    'name' => $file['name'],
                    'hash_name' => $file['hash_name'],
                    'path' => $file['path'],
                    'mime_type' => $file['mime_type'],
                    'size' => $file['size'],
                    'company_id' => $file['company_id'],
                    'user_id' => $file['user_id'],
                    'created_at' => $file['created_at'],
                    'updated_at' => $file['updated_at'],
                    'url' => Storage::url($file['path'])
                ];
            }, $files);
        } catch (Exception $e) {
            Log::error('Failed to retrieve files: ' . $e->getMessage());
            throw new Exception('Failed to retrieve files.');
        }
    }

    /**
     * Find a file by ID.
     *
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function findById(int $id): array
    {
        $file = $this->fileRepository->findById($id);
        if (!$file) {
            throw new NotFoundHttpException('File not found.');
        }
        return [
            'id' => $file['id'],
            'name' => $file['name'],
            'hash_name' => $file['hash_name'],
            'path' => $file['path'],
            'mime_type' => $file['mime_type'],
            'size' => $file['size'],
            'company_id' => $file['company_id'],
            'user_id' => $file['user_id'],
            'created_at' => $file['created_at'],
            'updated_at' => $file['updated_at'],
            'url' => Storage::url($file['path'])
        ];
    }

    /**
     * Delete a file by ID.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            $file = $this->fileRepository->findById($id);
            if (!$file) {
                throw new NotFoundHttpException('File not found.');
            }
            Storage::delete($file->path);
            return $this->fileRepository->delete($id);
        } catch (NotFoundHttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('File deletion failed: ' . $e->getMessage());
            throw new Exception('File deletion failed.');
        }
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
