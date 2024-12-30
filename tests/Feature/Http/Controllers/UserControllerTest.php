<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create([
            'email' => 'auth@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'auth@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        return [
            'token' => $response->json('token'),
            'user' => $user
        ];
    }

    public function test_store_user(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
            ->assertJson(['name' => 'John Doe']);
    }

    public function test_store_user_with_invalid_data(): void
    {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422); // Unprocessable Entity
    }

    public function test_show_user(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->getJson("/api/users/{$user->id}", [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJson(['name' => $user->name]);
    }

    public function test_update_user(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $data, [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJson(['name' => 'Jane Doe']);
    }

    public function test_delete_user(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->deleteJson("/api/users/{$user->id}", [], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_find_user_by_username(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->getJson("/api/users/username/{$user->name}", [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJson(['name' => $user->name]);
    }

    public function test_find_user_by_email(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->getJson("/api/users/email/{$user->email}", [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJson(['email' => $user->email]);
    }
}
