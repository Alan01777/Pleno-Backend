<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Company;
use App\Models\User;
use App\Models\File;

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
}
