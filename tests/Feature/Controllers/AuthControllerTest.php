<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ];

        $this->postJson('/api/register', $data);

        $this->postJson('/api/login', [
            'email' => $data['email'],
            'password' => $data['password'],
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function test_logout(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ];

        $this->postJson('/api/register', $data);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $token = $loginResponse->json('token');

        $this->postJson(
            '/api/logout',
            [],
            ['Authorization' => 'Bearer ' . $token]
        )
            ->assertStatus(200)
            ->assertJson(['message' => __('auth.logged_out')]);
    }
}
