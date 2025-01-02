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
     *
     * @return void
     */
    public function testAuthorize(): void
    {
        $request = new UserRequest();
        $this->assertTrue($request->authorize());
    }

    /**
     * Test the validation rules for POST method.
     *
     * @return void
     */
    public function testRulesForPostMethod(): void
    {
        $request = new UserRequest();
        $request->setMethod('POST');
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertEquals('required|string', $rules['name']);
        $this->assertEquals('required|email|unique:users,email', $rules['email']);
        $this->assertEquals('required|string|min:6', $rules['password']);
    }

    /**
     * Test the validation rules for non-POST methods.
     *
     * @return void
     */
    public function testRulesForNonPostMethod(): void
    {
        $request = new UserRequest();
        $request->setMethod('PUT'); // or any other non-POST method
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
     *
     * @return void
     */
    public function testMessages(): void
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
     * Test failed validation for the user request.
     *
     * @param array $invalidData
     * @param array $expectedErrors
     * @return void
     */
    #[DataProvider('invalidUserDataProvider')]
    public function testFailedValidation(array $invalidData, array $expectedErrors): void
    {
        $response = $this->postJson(self::REGISTER_URL, $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($expectedErrors);
    }

    /**
     * Data provider for testFailedValidation.
     *
     * @return array
     */
    public static function invalidUserDataProvider(): array
    {
        return [
            [[], ['name', 'email', 'password']],
            [['name' => '', 'email' => 'invalid-email', 'password' => 'short'], ['name', 'email', 'password']],
            [['name' => 'John Doe', 'email' => 'invalid-email', 'password' => 'short'], ['email', 'password']],
        ];
    }

    /**
     * Test successful validation for the user request.
     *
     * @return void
     */
    public function testSuccessfulValidation(): void
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
     * Tear down the test environment.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
