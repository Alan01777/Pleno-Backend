<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\FileRepository;
use App\Models\File;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FileRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $fileRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileRepository = new FileRepository();
    }

    public function testCreate()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $data = [
            'name' => 'Test File',
            'company_id' => $company->id,
            'hash_name' => 'test_hash',
            'path' => 'test_path',
            'mime_type' => 'text/plain',
            'size' => 12345,
            'user_id' => $user->id
        ];
        $result = $this->fileRepository->create($data);
        $this->assertInstanceOf(File::class, $result);
        $this->assertDatabaseHas('files', $data);
    }

    public function testFindById()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $file = File::factory()->create(['user_id' => $user->id, 'company_id' => $company->id]);
        $result = $this->fileRepository->findById($file->id);
        $this->assertInstanceOf(File::class, $result);
        $this->assertEquals($file->id, $result->id);
    }

    public function testFindAllByUserId()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $files = File::factory()->count(3)->create(['user_id' => $user->id, 'company_id' => $company->id]);
        $result = $this->fileRepository->findAllByUserId([$company->id]);
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }

    public function testUpdate()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $file = File::factory()->create(['user_id' => $user->id, 'company_id' => $company->id]);
        $data = ['name' => 'Updated File'];
        $result = $this->fileRepository->update($file->id, $data);
        $this->assertTrue($result);
        $this->assertDatabaseHas('files', array_merge(['id' => $file->id], $data));
    }

    public function testDelete()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $file = File::factory()->create(['user_id' => $user->id, 'company_id' => $company->id]);
        $result = $this->fileRepository->delete($file->id);
        $this->assertTrue($result);
        $this->assertDatabaseMissing('files', ['id' => $file->id]);
    }
}
