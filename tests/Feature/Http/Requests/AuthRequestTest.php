<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\AuthRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class AuthRequestTest
 *
 * This class contains tests for the AuthRequest class, which handles the authorization
 * and validation logic for authentication requests in the application.
 *
 * @package Tests\Feature\Http\Requests
 */
class AuthRequestTest extends TestCase
{
    use RefreshDatabase;

    private const LOGIN_URL = '/api/login';

    /**
     * Test that the AuthRequest is authorized.
     *
     * @return void
     */
    public function testAuthorize(): void
    {
        $request = new AuthRequest();
        $this->assertTrue($request->authorize());
    }

    /**
     * Test the validation rules.
     *
     * @return void
     */
    public function testRules(): void
    {
        $request = new AuthRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertEquals('required|email', $rules['email']);
        $this->assertEquals('required|string|min:6', $rules['password']);
    }

    /**
     * Test the custom validation messages.
     *
     * @return void
     */
    public function testMessages(): void
    {
        $request = new AuthRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('email.required', $messages);
        $this->assertArrayHasKey('email.email', $messages);
        $this->assertArrayHasKey('password.required', $messages);
        $this->assertArrayHasKey('password.min', $messages);
        $this->assertEquals('Email is required', $messages['email.required']);
        $this->assertEquals('Email is invalid', $messages['email.email']);
        $this->assertEquals('Password is required', $messages['password.required']);
        $this->assertEquals('Password must be at least 6 characters', $messages['password.min']);
    }

    /**
     * Test failed validation for the login request.
     *
     * @param array $invalidData
     * @param array $expectedErrors
     * @return void
     */
    #[DataProvider('invalidLoginDataProvider')]
    public function testFailedValidation(array $invalidData, array $expectedErrors): void
    {
        $response = $this->postJson(self::LOGIN_URL, $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($expectedErrors);
    }

    /**
     * Data provider for testFailedValidation.
     *
     * @return array
     */
    public static function invalidLoginDataProvider(): array
    {
        return [
            [[], ['email', 'password']],
            [['email' => 'invalid-email'], ['email', 'password']],
            [['password' => 'short'], ['email', 'password']],
            [['email' => 'invalid-email', 'password' => 'short'], ['email', 'password']],
        ];
    }

    /**
     * Test successful validation for the login request.
     *
     * @return void
     */
    public function testSuccessfulValidation(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $validData = [
            'email' => 'john@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson(self::LOGIN_URL, $validData);

        $response->assertStatus(200);
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
