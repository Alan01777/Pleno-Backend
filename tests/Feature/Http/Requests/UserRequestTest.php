<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\UserRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class UserRequestTest
 *
 * This class contains feature tests for the UserRequest class.
 * It tests the authorization, validation rules, custom messages, and failed validation scenarios.
 *
 * @package Tests\Feature\Http\Requests
 */

class UserRequestTest extends TestCase
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
            'password' => bcrypt('password'),
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
     * Test that the UserRequest is authorized.
     */
    public function test_authorize(): void
    {
        $request = new UserRequest();
        $this->assertTrue($request->authorize());
    }

    /**
     * Test the validation rules for the POST method.
     */
    public function test_rules_for_post_method(): void
    {
        // Create a new request instance with the POST method
        $request = UserRequest::create(self::USERS_URL, 'POST');

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertEquals('required|string', $rules['name']);
        $this->assertEquals('required|email|unique:users,email', $rules['email']);
        $this->assertEquals('required|string|min:6', $rules['password']);
    }

    /**
     * Test the validation rules for other methods (e.g., PUT).
     */
    public function test_rules_for_other_methods(): void
    {
        $request = new UserRequest();

        // Simulate a PUT request
        $this->app['request']->setMethod('PUT');

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertEquals('string', $rules['name']);
        $this->assertEquals('email|unique:users,email', $rules['email']);
        $this->assertEquals('string|min:6', $rules['password']);
    }

    /**
     * Test the custom validation messages.
     */
    public function test_messages(): void
    {
        $request = new UserRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('name.required', $messages);
        $this->assertArrayHasKey('email.required', $messages);
        $this->assertArrayHasKey('email.email', $messages);
        $this->assertArrayHasKey('email.unique', $messages);
        $this->assertArrayHasKey('password.required', $messages);
        $this->assertArrayHasKey('password.min', $messages);
        $this->assertEquals('Name is required', $messages['name.required']);
        $this->assertEquals('Email is required', $messages['email.required']);
        $this->assertEquals('Email is invalid', $messages['email.email']);
        $this->assertEquals('Email is already taken', $messages['email.unique']);
        $this->assertEquals('Password is required', $messages['password.required']);
        $this->assertEquals('Password must be at least 6 characters', $messages['password.min']);
    }

    /**
     * Test failed validation for the POST method.
     */
    public function test_failed_validation_for_post_method(): void
    {
        $response = $this->postJson(self::REGISTER_URL, []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test failed validation for the PUT method with invalid data.
     */
    #[DataProvider('invalidPutDataProvider')]
    public function test_failed_validation_for_put_method(array $invalidData, array $expectedErrors): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $response = $this->putJson(self::USERS_URL . "/{$user->id}", $invalidData, [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($expectedErrors);
    }

    /**
     * Data provider for test_failed_validation_for_put_method.
     *
     * @return array
     */
    public static function invalidPutDataProvider(): array
    {
        return [
            [['email' => 'invalid-email'], ['email']],
            [['password' => 'short'], ['password']],
        ];
    }

    /**
     * Test successful validation for the POST method.
     */
    public function test_successful_validation_for_post_method(): void
    {
        $validData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson(self::REGISTER_URL, $validData);

        $response->assertStatus(201);
    }

    /**
     * Test successful validation for the PUT method.
     */
    public function test_successful_validation_for_put_method(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        $validData = [
            'name' => 'John Doe Updated',
            'email' => 'john.updated@example.com',
            'password' => 'newpassword',
        ];

        $response = $this->putJson(self::USERS_URL . "/{$user->id}", $validData, [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200);
    }
}
