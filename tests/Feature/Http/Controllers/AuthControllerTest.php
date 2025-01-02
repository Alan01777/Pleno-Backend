<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthControllerTest
 *
 * This class contains feature tests for the AuthController.
 * It tests the user login, logout, and authentication functionalities.
 *
 * @package Tests\Feature\Http\Controllers
 */
class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Register a user and return the user data.
     *
     * @param array $overrides
     * @return array
     */
    protected function registerUser($overrides = []): array
    {
        $data = array_merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ], $overrides);

        $this->postJson('/api/register', $data);

        return $data;
    }

    /**
     * Login a user and return the response.
     *
     * @param string $email
     * @param string $password
     * @return \Illuminate\Testing\TestResponse
     */
    protected function loginUser($email, $password)
    {
        return $this->postJson('/api/login', [
            'email' => $email,
            'password' => $password,
        ]);
    }

    /**
     * Test user login with valid credentials.
     *
     * @return void
     */
    public function testLogin(): void
    {
        $data = $this->registerUser();

        $this->loginUser($data['email'], $data['password'])
            ->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    /**
     * Test user login with invalid credentials.
     *
     * @return void
     */
    public function testLoginWithInvalidCredentials(): void
    {
        $data = $this->registerUser();

        $this->loginUser($data['email'], 'invalid-password')
            ->assertStatus(401)
            ->assertJson(['message' => __('auth.failed')]);
    }

    /**
     * Test user logout.
     *
     * @return void
     */
    public function testLogout(): void
    {
        $data = $this->registerUser();

        $loginResponse = $this->loginUser($data['email'], $data['password']);

        $token = $loginResponse->json('token');

        $this->postJson(
            '/api/logout',
            [],
            ['Authorization' => 'Bearer ' . $token]
        )
            ->assertStatus(200)
            ->assertJson(['message' => __('auth.logged_out')]);
    }

    /**
     * Test login with nonexistent user.
     *
     * @return void
     */
    public function testLoginWithNonexistentUser(): void
    {
        $this->loginUser('nonexistent@example.com', 'password')
            ->assertStatus(401)
            ->assertJson(['message' => __('auth.failed')]);
    }

    /**
     * Test logout without token.
     *
     * @return void
     */
    public function testLogoutWithoutToken(): void
    {
        $this->postJson('/api/logout')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Test registration with valid data.
     *
     * @return void
     */

    public function testRegisterWithValidData(): void
    {
        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'name',
                'email',
                'updated_at',
                'created_at',
                'id'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
        ]);
    }

    /**
     * Test registration with invalid data.
     *
     * @return void
     */
    public function testRegisterWithInvalidData(): void
    {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
