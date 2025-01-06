<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Contracts\Services\UserServiceInterface;
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
    public function testStoreUser(array $data): void
    {
        $response = $this->postJson(self::REGISTER_URL, $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => $data['name'] ?? '']);

        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
        ]);
    }

    /**
     * Test storing a user with invalid data.
     *
     * @param array $data
     * @return void
     */
    #[DataProvider('invalidUserDataProvider')]
    public function testStoreUserWithInvalidData(array $data): void
    {
        $response = $this->postJson(self::REGISTER_URL, $data);

        $response->assertStatus(422);

        if (isset($data['name']) && empty($data['name'])) {
            $response->assertJsonValidationErrors(['name']);
        }

        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $response->assertJsonValidationErrors(['email']);
        }

        if (isset($data['password']) && strlen($data['password']) < 6) {
            $response->assertJsonValidationErrors(['password']);
        }
    }

    /**
     * Test showing the authenticated user.
     *
     * @return void
     */
    public function testShowUser(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->getJson(self::USERS_URL . '/user', [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $user->name, 'email' => $user->email]);
    }

    /**
     * Test updating the authenticated user with valid data.
     *
     * @param array $data
     * @return void
     */
    #[DataProvider('userDataProvider')]
    public function testUpdateUser(array $data): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->putJson(self::USERS_URL . '/user', $data, [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $data['name'] ?? '']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $data['name'],
        ]);
    }

    /**
     * Test deleting the authenticated user.
     *
     * @return void
     */
    public function testDeleteUser(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->deleteJson(self::USERS_URL . '/user', [], [
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
    public function testFindUserByUsername(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->getJson(self::USERS_URL . "/username/{$user->name}", [
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
    public function testFindUserByEmail(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->getJson(self::USERS_URL . "/email/{$user->email}", [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => $user->email]);
    }

    /**
     * Test handling an exception in the UserController.
     *
     * @return void
     */
    public function testHandleException(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];

        // Simulate an exception in the UserService
        $this->app->instance(UserServiceInterface::class, new class implements UserServiceInterface {
            public function create(array $data): array
            {
                throw new Exception('Test exception');
            }
            public function findById(int $id): array
            {
                throw new Exception('Test exception');
            }
            public function update(int $id, array $data): array
            {
                throw new Exception('Test exception');
            }
            public function delete(int $id): bool
            {
                throw new Exception('Test exception');
            }
            public function findByUsername(string $username): array
            {
                throw new Exception('Test exception');
            }
            public function findByEmail(string $email): array
            {
                throw new Exception('Test exception');
            }
            public function findAll(): array
            {
                throw new Exception('Test exception');
            }
        });

        $response = $this->getJson(self::USERS_URL . '/user', [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Test exception']);
    }

    /**
     * Test handling a NotFoundHttpException in the UserController.
     *
     * @return void
     */
    public function testHandleNotFoundHttpException(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];

        // Simulate a NotFoundHttpException in the UserService
        $this->app->instance(UserServiceInterface::class, new class implements UserServiceInterface {
            public function create(array $data): array
            {
                throw new NotFoundHttpException('User not found.');
            }
            public function findById(int $id): array
            {
                throw new NotFoundHttpException('User not found.');
            }
            public function update(int $id, array $data): array
            {
                throw new NotFoundHttpException('User not found.');
            }
            public function delete(int $id): bool
            {
                throw new NotFoundHttpException('User not found.');
            }
            public function findByUsername(string $username): array
            {
                throw new NotFoundHttpException('User not found.');
            }
            public function findByEmail(string $email): array
            {
                throw new NotFoundHttpException('User not found.');
            }
            public function findAll(): array
            {
                throw new NotFoundHttpException('User not found.');
            }
        });

        $response = $this->getJson(self::USERS_URL . '/user', [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'User not found.']);
    }

    /**
     * Test handling an exception in the update method.
     *
     * @return void
     */
    public function testHandleExceptionInUpdate(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        // Simulate an exception in the UserService
        $this->app->instance(UserServiceInterface::class, new class implements UserServiceInterface {
            public function create(array $data): array
            {
                return [];
            }
            public function findById(int $id): array
            {
                return [];
            }
            public function update(int $id, array $data): array
            {
                throw new Exception('Test exception');
            }
            public function delete(int $id): bool
            {
                return true;
            }
            public function findByUsername(string $username): array
            {
                return [];
            }
            public function findByEmail(string $email): array
            {
                return [];
            }
            public function findAll(): array
            {
                return [];
            }
        });

        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password'
        ];

        $response = $this->putJson(self::USERS_URL . '/user', $data, [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Test exception']);
    }

    /**
     * Test handling an exception in the delete method.
     *
     * @return void
     */
    public function testHandleExceptionInDelete(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        // Simulate an exception in the UserService
        $this->app->instance(UserServiceInterface::class, new class implements UserServiceInterface {
            public function create(array $data): array
            {
                return [];
            }
            public function findById(int $id): array
            {
                return [];
            }
            public function update(int $id, array $data): array
            {
                return [];
            }
            public function delete(int $id): bool
            {
                throw new Exception('Test exception');
            }
            public function findByUsername(string $username): array
            {
                return [];
            }
            public function findByEmail(string $email): array
            {
                return [];
            }
            public function findAll(): array
            {
                return [];
            }
        });

        $response = $this->deleteJson(self::USERS_URL . '/user', [], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Test exception']);
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
