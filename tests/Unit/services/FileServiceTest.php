<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\FileService;
use App\Contracts\Repositories\FileRepositoryInterface;
use App\Contracts\Services\CompanyServiceInterface;
use App\Models\File;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;
use Mockery;

class FileServiceTest extends TestCase
{
    protected $fileService;
    protected $fileRepository;
    protected $companyService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileRepository = Mockery::mock(FileRepositoryInterface::class);
        $this->companyService = Mockery::mock(CompanyServiceInterface::class);
        $this->fileService = new FileService($this->fileRepository, $this->companyService);
    }

    public function testCreateFile()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $companyId = 1;

        $this->fileRepository->shouldReceive('create')->once()->andReturn(new \App\Models\File());

        $result = $this->fileService->create($file, $companyId);

        $this->assertInstanceOf(\App\Models\File::class, $result);
        Storage::assertExists('files/' . $result->hash_name);
    }

    public function testUpdateFile()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $companyId = 1;
        $fileId = 1;

        $existingFile = new \App\Models\File(['path' => 'files/old_document.pdf']);
        $this->fileRepository->shouldReceive('findById')->once()->with($fileId)->andReturn($existingFile);
        $this->fileRepository->shouldReceive('update')->once()->andReturn(true);

        // Ensure the old file exists in the fake storage
        Storage::put('files/old_document.pdf', 'old content');

        // Generate the new file path
        $newFilePath = 'files/' . $file->hashName();

        // Store the new file in the fake storage
        Storage::put($newFilePath, 'new content');

        $result = $this->fileService->update($fileId, $file, $companyId);

        $this->assertTrue($result);
        Storage::assertMissing('files/old_document.pdf');
        Storage::assertExists($newFilePath);
    }

    public function testFindAllByUserId()
    {
        $companies = [['id' => 1], ['id' => 2]];
        $files = [
            new \App\Models\File(['id' => 1, 'name' => 'file1', 'path' => 'files/file1.pdf']),
            new \App\Models\File(['id' => 2, 'name' => 'file2', 'path' => 'files/file2.pdf'])
        ];

        $this->companyService->shouldReceive('findAllByUserId')->once()->andReturn($companies);
        $this->fileRepository->shouldReceive('findAllByUserId')->once()->andReturn($files);

        $result = $this->fileService->findAllByUserId();

        $this->assertCount(2, $result);
        $this->assertEquals('file1', $result[0]['name']);
        $this->assertEquals('file2', $result[1]['name']);
    }

    public function testFindById()
    {
        $fileId = 1;
        $file = new \App\Models\File(['id' => 1, 'name' => 'file1', 'path' => 'files/file1.pdf']);

        $this->fileRepository->shouldReceive('findById')->once()->with($fileId)->andReturn($file);

        $result = $this->fileService->findById($fileId);

        $this->assertEquals('file1', $result['name']);
    }

    public function testDeleteFile()
    {
        $fileId = 1;
        $file = new \App\Models\File(['path' => 'files/document.pdf']);

        $this->fileRepository->shouldReceive('findById')->once()->with($fileId)->andReturn($file);
        $this->fileRepository->shouldReceive('delete')->once()->andReturn(true);

        // Ensure the file exists in the fake storage
        Storage::put('files/document.pdf', 'content');

        $result = $this->fileService->delete($fileId);

        $this->assertTrue($result);
        Storage::assertMissing('files/document.pdf');
    }

    public function testHandleExceptionInCreate()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $companyId = 1;

        $this->fileRepository->shouldReceive('create')->once()->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File creation failed.');

        $this->fileService->create($file, $companyId);
    }

    public function testHandleExceptionInUpdate()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $companyId = 1;
        $fileId = 1;

        $existingFile = new \App\Models\File(['path' => 'files/old_document.pdf']);
        $this->fileRepository->shouldReceive('findById')->once()->with($fileId)->andReturn($existingFile);
        $this->fileRepository->shouldReceive('update')->once()->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File update failed.');

        $this->fileService->update($fileId, $file, $companyId);
    }

    public function testHandleExceptionInUpdateWithNoFile()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $companyId = 1;
        $fileId = 1;

        $existingFile = null;
        $this->fileRepository->shouldReceive('findById')->once()->with($fileId)->andReturn($existingFile);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('File not found.');

        $this->fileService->update($fileId, $file, $companyId);
    }

    public function testHandleExceptionInFindAllByUserId()
    {
        $this->companyService->shouldReceive('findAllByUserId')->once()->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to retrieve files.');

        $this->fileService->findAllByUserId();
    }

    public function testHandleExceptionInFindById()
    {
        $fileId = 1;

        $this->fileRepository->shouldReceive('findById')->once()->with($fileId)->andThrow(new NotFoundHttpException('File not found.'));

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('File not found.');

        $this->fileService->findById($fileId);
    }

    public function testHandleExceptionInDelete()
    {
        $fileId = 1;

        $this->fileRepository->shouldReceive('findById')->once()->with($fileId)->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File deletion failed.');

        $this->fileService->delete($fileId);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
