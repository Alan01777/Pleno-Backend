<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Company;
use App\Models\User;
use App\Models\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;
use App\Contracts\Services\FileServiceInterface;

class FileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['user_id' => $this->user->id]);
    }

    protected function authenticate()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        return $response->json('token');
    }

    public function testStore()
    {
        $token = $this->authenticate();
        $file = UploadedFile::fake()->image('test.jpg');
        $company = $this->company;

        $response = $this->postJson('/api/files', [
            'company_id' => $company->id,
            'file' => $file,
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id', 'name', 'hash_name', 'path', 'mime_type', 'size', 'company_id', 'user_id', 'created_at', 'updated_at'
        ]);
    }

    public function testStoreWithInvalidData()
    {
        $token = $this->authenticate();
        $company = $this->company;

        $response = $this->postJson('/api/files', [
            'company_id' => $company->id,
            'file' => 'not-a-file',
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    public function testUpdate()
    {
        $token = $this->authenticate();
        $file = UploadedFile::fake()->image('test.jpg');
        $company = $this->company;

        // Create a file record in the database first
        $createdFile = File::factory()->create([
            'company_id' => $company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->putJson('/api/files/' . $createdFile->id, [
            'company_id' => $company->id,
            'file' => $file,
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200);
        $response->assertJson(['updated' => true]);
    }

    public function testUpdateWithInvalidData()
    {
        $token = $this->authenticate();
        $company = $this->company;

        // Create a file record in the database first
        $createdFile = File::factory()->create([
            'company_id' => $company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->putJson('/api/files/' . $createdFile->id, [
            'company_id' => $company->id,
            'file' => 'not-a-file',
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    public function testDestroy()
    {
        $token = $this->authenticate();

        // Create a file record in the database first
        $createdFile = File::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson('/api/files/' . $createdFile->id, [], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200);
        $response->assertJson(['deleted' => true]);
    }

    public function testDestroyWithInvalidId()
    {
        $token = $this->authenticate();

        $response = $this->deleteJson('/api/files/999', [], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(404);
    }

    public function testIndex()
    {
        $token = $this->authenticate();

        // Create some file records in the database
        File::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/files', [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => ['id', 'name', 'hash_name', 'path', 'mime_type', 'size', 'company_id', 'user_id', 'created_at', 'updated_at', 'url']
        ]);
    }

    public function testShow()
    {
        $token = $this->authenticate();

        // Create a file record in the database first
        $createdFile = File::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/files/' . $createdFile->id, [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'name', 'hash_name', 'path', 'mime_type', 'size', 'company_id', 'user_id', 'created_at', 'updated_at', 'url'
        ]);
    }

    public function testShowWithInvalidId()
    {
        $token = $this->authenticate();

        $response = $this->getJson('/api/files/999', [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(404);
    }

    public function testStoreHandlesException()
    {
        $token = $this->authenticate();
        $file = UploadedFile::fake()->image('test.jpg');
        $company = $this->company;

        // Simulate an exception in the FileService
        $this->app->instance(FileServiceInterface::class, new class implements FileServiceInterface {
            public function create(UploadedFile $file, int $companyId): File { throw new Exception('Test exception'); }
            public function findById(int $id): array { throw new Exception('Test exception'); }
            public function update(int $id, UploadedFile $file, int $companyId): bool { throw new Exception('Test exception'); }
            public function delete(int $id): bool { throw new Exception('Test exception'); }
            public function findAllByUserId(): array { throw new Exception('Test exception'); }
        });

        $response = $this->postJson('/api/files', [
            'company_id' => $company->id,
            'file' => $file,
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Test exception']);
    }

    public function testUpdateHandlesException()
    {
        $token = $this->authenticate();
        $file = UploadedFile::fake()->image('test.jpg');
        $company = $this->company;

        // Create a file record in the database first
        $createdFile = File::factory()->create([
            'company_id' => $company->id,
            'user_id' => $this->user->id,
        ]);

        // Simulate an exception in the FileService
        $this->app->instance(FileServiceInterface::class, new class implements FileServiceInterface {
            public function create(UploadedFile $file, int $companyId): File { throw new Exception('Test exception'); }
            public function findById(int $id): array { throw new Exception('Test exception'); }
            public function update(int $id, UploadedFile $file, int $companyId): bool { throw new Exception('Test exception'); }
            public function delete(int $id): bool { throw new Exception('Test exception'); }
            public function findAllByUserId(): array { throw new Exception('Test exception'); }
        });

        $response = $this->putJson('/api/files/' . $createdFile->id, [
            'company_id' => $company->id,
            'file' => $file,
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Test exception']);
    }

    public function testDestroyHandlesException()
    {
        $token = $this->authenticate();

        // Create a file record in the database first
        $createdFile = File::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        // Simulate an exception in the FileService
        $this->app->instance(FileServiceInterface::class, new class implements FileServiceInterface {
            public function create(UploadedFile $file, int $companyId): File { throw new Exception('Test exception'); }
            public function findById(int $id): array { throw new Exception('Test exception'); }
            public function update(int $id, UploadedFile $file, int $companyId): bool { throw new Exception('Test exception'); }
            public function delete(int $id): bool { throw new Exception('Test exception'); }
            public function findAllByUserId(): array { throw new Exception('Test exception'); }
        });

        $response = $this->deleteJson('/api/files/' . $createdFile->id, [], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Test exception']);
    }

    public function testShowHandlesException()
    {
        $token = $this->authenticate();

        // Create a file record in the database first
        $createdFile = File::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        // Simulate an exception in the FileService
        $this->app->instance(FileServiceInterface::class, new class implements FileServiceInterface {
            public function create(UploadedFile $file, int $companyId): File { throw new Exception('Test exception'); }
            public function findById(int $id): array { throw new Exception('Test exception'); }
            public function update(int $id, UploadedFile $file, int $companyId): bool { throw new Exception('Test exception'); }
            public function delete(int $id): bool { throw new Exception('Test exception'); }
            public function findAllByUserId(): array { throw new Exception('Test exception'); }
        });

        $response = $this->getJson('/api/files/' . $createdFile->id, [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Test exception']);
    }

    public function testShowHandlesNotFoundHttpException()
    {
        $token = $this->authenticate();

        // Simulate a NotFoundHttpException in the FileService
        $this->app->instance(FileServiceInterface::class, new class implements FileServiceInterface {
            public function create(UploadedFile $file, int $companyId): File { throw new NotFoundHttpException('File not found.'); }
            public function findById(int $id): array { throw new NotFoundHttpException('File not found.'); }
            public function update(int $id, UploadedFile $file, int $companyId): bool { throw new NotFoundHttpException('File not found.'); }
            public function delete(int $id): bool { throw new NotFoundHttpException('File not found.'); }
            public function findAllByUserId(): array { throw new NotFoundHttpException('File not found.'); }
        });

        $response = $this->getJson('/api/files/999', [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'File not found.']);
    }

    public function testUpdateHandlesNotFoundHttpException()
    {
        $token = $this->authenticate();
        $file = UploadedFile::fake()->image('test.jpg');
        $company = $this->company;

        // Simulate a NotFoundHttpException in the FileService
        $this->app->instance(FileServiceInterface::class, new class implements FileServiceInterface {
            public function create(UploadedFile $file, int $companyId): File { throw new NotFoundHttpException('File not found.'); }
            public function findById(int $id): array { throw new NotFoundHttpException('File not found.'); }
            public function update(int $id, UploadedFile $file, int $companyId): bool { throw new NotFoundHttpException('File not found.'); }
            public function delete(int $id): bool { throw new NotFoundHttpException('File not found.'); }
            public function findAllByUserId(): array { throw new NotFoundHttpException('File not found.'); }
        });

        $response = $this->putJson('/api/files/999', [
            'company_id' => $company->id,
            'file' => $file,
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'File not found.']);
    }

    public function testDestroyHandlesNotFoundHttpException()
    {
        $token = $this->authenticate();

        // Simulate a NotFoundHttpException in the FileService
        $this->app->instance(FileServiceInterface::class, new class implements FileServiceInterface {
            public function create(UploadedFile $file, int $companyId): File { throw new NotFoundHttpException('File not found.'); }
            public function findById(int $id): array { throw new NotFoundHttpException('File not found.'); }
            public function update(int $id, UploadedFile $file, int $companyId): bool { throw new NotFoundHttpException('File not found.'); }
            public function delete(int $id): bool { throw new NotFoundHttpException('File not found.'); }
            public function findAllByUserId(): array { throw new NotFoundHttpException('File not found.'); }
        });

        $response = $this->deleteJson('/api/files/999', [], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'File not found.']);
    }
}
