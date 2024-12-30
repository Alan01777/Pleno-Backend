<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class UserControllerTest
 *
 * This class contains feature tests for the UserController.
 * It tests the user registration, login, retrieval, update, and deletion functionalities.
 *
 * @package Tests\Feature\Controllers
 */

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private const REGISTER_URL = '/api/register';
    private const LOGIN_URL = '/api/login';
    private const USERS_URL = '/api/users';

    /**
     * Authenticate a user and return the token and user.
     *
     * @return array
     */
    protected function authenticate(): array
    {
        $user = User::factory()->create([
            'email' => 'auth@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson(self::LOGIN_URL, [
            'email' => 'auth@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        return [
            'token' => $response->json('token'),
            'user' => $user
        ];
    }

    /**
     * Test storing a user with valid data.
     *
     * @param array $data
     * @return void
     */
    #[DataProvider('userDataProvider')]
    public function test_store_user($data): void
    {
        $response = $this->postJson(self::REGISTER_URL, $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => $data['name'] ?? '']);
    }

    /**
     * Test storing a user with invalid data.
     *
     * @param array $data
     * @return void
     */
    #[DataProvider('invalidUserDataProvider')]
    public function test_store_user_with_invalid_data($data): void
    {
        $response = $this->postJson(self::REGISTER_URL, $data);

        $response->assertStatus(422);
    }

    /**
     * Test showing a user.
     *
     * @return void
     */
    public function test_show_user(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->getJson(self::USERS_URL . '/' . $user->id, [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $user->name, 'email' => $user->email]);
    }

    /**
     * Test updating a user with valid data.
     *
     * @param array $data
     * @return void
     */
    #[DataProvider('userDataProvider')]
    public function test_update_user($data): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->putJson("/api/users/{$user->id}", $data, [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $data['name'] ?? '']);
    }

    /**
     * Test deleting a user.
     *
     * @return void
     */
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

    /**
     * Test finding a user by username.
     *
     * @return void
     */
    public function test_find_user_by_username(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->getJson("/api/users/username/{$user->name}", [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $user->name]);
    }

    /**
     * Test finding a user by email.
     *
     * @return void
     */
    public function test_find_user_by_email(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->getJson("/api/users/email/{$user->email}", [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => $user->email]);
    }

    /**
     * Data provider for valid user data.
     *
     * @return array
     */
    public static function userDataProvider(): array
    {
        return [
            [['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'password']],
            [['name' => 'Jane Doe', 'email' => 'jane@example.com', 'password' => 'password']],
        ];
    }

    /**
     * Data provider for invalid user data.
     *
     * @return array
     */
    public static function invalidUserDataProvider(): array
    {
        return [
            [['name' => '', 'email' => 'john@example.com', 'password' => 'password']],
            [['name' => 'John Doe', 'email' => 'invalid-email', 'password' => 'password']],
            [['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'short']],
        ];
    }
}
