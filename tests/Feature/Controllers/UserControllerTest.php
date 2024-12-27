<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_user(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/users', $data);

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

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(422); // Unprocessable Entity
    }

    public function test_show_user(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson(['name' => $user->name]);
    }

    public function test_update_user(): void
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $data);

        $response->assertStatus(200)
            ->assertJson(['name' => 'Jane Doe']);
    }

    public function test_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_find_user_by_username(): void
    {
        $user = User::factory()->create(['name' => 'John Doe']);

        $response = $this->getJson("/api/users/username/{$user->name}");

        $response->assertStatus(200)
            ->assertJson(['name' => 'John Doe']);
    }

    public function test_find_user_by_email(): void
    {
        $user = User::factory()->create(['email' => 'john@example.com']);

        $response = $this->getJson("/api/users/email/{$user->email}");

        $response->assertStatus(200)
            ->assertJson(['email' => 'john@example.com']);
    }
}
