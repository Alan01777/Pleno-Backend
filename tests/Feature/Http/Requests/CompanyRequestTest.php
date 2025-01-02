<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\CompanyRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class CompanyRequestTest
 *
 * This class contains feature tests for the CompanyRequest class.
 * It tests the authorization, validation rules, custom messages, and failed validation scenarios.
 *
 * @package Tests\Feature\Http\Requests
 */
class CompanyRequestTest extends TestCase
{
    use RefreshDatabase;

    private const COMPANIES_URL = '/api/companies';

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

    /**
     * Test that the CompanyRequest is authorized.
     *
     * @return void
     */
    public function testAuthorize(): void
    {
        $request = new CompanyRequest();
        $this->assertTrue($request->authorize());
    }

    /**
     * Test the validation rules for POST method.
     *
     * @return void
     */
    public function testRulesForPostMethod(): void
    {
        $request = new CompanyRequest();
        $request->setMethod('POST');
        $rules = $request->rules();

        $this->assertArrayHasKey('cnpj', $rules);
        $this->assertArrayHasKey('trade_name', $rules);
        $this->assertArrayHasKey('legal_name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('phone', $rules);
        $this->assertArrayHasKey('address', $rules);
        $this->assertArrayHasKey('size', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertEquals('required|string|max:14|unique:companies,cnpj', $rules['cnpj']);
        $this->assertEquals('string|max:255|unique:companies,trade_name', $rules['trade_name']);
        $this->assertEquals('required|string|max:255', $rules['legal_name']);
        $this->assertEquals('required|email|max:255', $rules['email']);
        $this->assertEquals('required|string|max:20', $rules['phone']);
        $this->assertEquals('required|string|max:255', $rules['address']);
        $this->assertEquals('required|string|in:MEI,ME,EPP,EMP,EG', $rules['size']);
        $this->assertEquals('required|integer|exists:users,id', $rules['user_id']);
    }

    /**
     * Test the validation rules for non-POST methods.
     *
     * @return void
     */
    public function testRulesForNonPostMethod(): void
    {
        $request = new CompanyRequest();
        $request->setMethod('PUT'); // or any other non-POST method
        $rules = $request->rules();

        $this->assertArrayHasKey('cnpj', $rules);
        $this->assertArrayHasKey('trade_name', $rules);
        $this->assertArrayHasKey('legal_name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('phone', $rules);
        $this->assertArrayHasKey('address', $rules);
        $this->assertArrayHasKey('size', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertEquals('string|max:14', $rules['cnpj']);
        $this->assertEquals('string|max:255|unique:companies,trade_name', $rules['trade_name']);
        $this->assertEquals('string|max:255|unique:companies,legal_name', $rules['legal_name']);
        $this->assertEquals('email|max:255', $rules['email']);
        $this->assertEquals('string|max:20', $rules['phone']);
        $this->assertEquals('string|max:255', $rules['address']);
        $this->assertEquals('string|in:MEI,ME,EPP,EMP,EG', $rules['size']);
        $this->assertEquals('integer|exists:users,id', $rules['user_id']);
    }

    /**
     * Test the custom validation messages.
     *
     * @return void
     */
    public function testMessages(): void
    {
        $request = new CompanyRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('cnpj.required', $messages);
        $this->assertArrayHasKey('cnpj.max', $messages);
        $this->assertArrayHasKey('cnpj.unique', $messages);
        $this->assertArrayHasKey('trade_name.max', $messages);
        $this->assertArrayHasKey('trade_name.unique', $messages);
        $this->assertArrayHasKey('legal_name.required', $messages);
        $this->assertArrayHasKey('legal_name.max', $messages);
        $this->assertArrayHasKey('legal_name.unique', $messages);
        $this->assertArrayHasKey('email.required', $messages);
        $this->assertArrayHasKey('email.email', $messages);
        $this->assertArrayHasKey('email.max', $messages);
        $this->assertArrayHasKey('phone.required', $messages);
        $this->assertArrayHasKey('phone.max', $messages);
        $this->assertArrayHasKey('address.required', $messages);
        $this->assertArrayHasKey('address.max', $messages);
        $this->assertArrayHasKey('size.required', $messages);
        $this->assertArrayHasKey('size.in', $messages);
        $this->assertArrayHasKey('user_id.required', $messages);
        $this->assertArrayHasKey('user_id.integer', $messages);
        $this->assertArrayHasKey('user_id.exists', $messages);
        $this->assertEquals('CNPJ is required', $messages['cnpj.required']);
        $this->assertEquals('CNPJ must have at most 14 characters', $messages['cnpj.max']);
        $this->assertEquals('CNPJ must be unique', $messages['cnpj.unique']);
        $this->assertEquals('Trade name must have at most 255 characters', $messages['trade_name.max']);
        $this->assertEquals('Trade name must be unique', $messages['trade_name.unique']);
        $this->assertEquals('Legal name is required', $messages['legal_name.required']);
        $this->assertEquals('Legal name must have at most 255 characters', $messages['legal_name.max']);
        $this->assertEquals('Legal name must be unique', $messages['legal_name.unique']);
        $this->assertEquals('Email is required', $messages['email.required']);
        $this->assertEquals('Email must be a valid email address', $messages['email.email']);
        $this->assertEquals('Email must have at most 255 characters', $messages['email.max']);
        $this->assertEquals('Phone is required', $messages['phone.required']);
        $this->assertEquals('Phone must have at most 20 characters', $messages['phone.max']);
        $this->assertEquals('Address is required', $messages['address.required']);
        $this->assertEquals('Address must have at most 255 characters', $messages['address.max']);
        $this->assertEquals('Size is required', $messages['size.required']);
        $this->assertEquals('Size must be one of MEI, ME, EPP, EMP, EG', $messages['size.in']);
        $this->assertEquals('User ID is required', $messages['user_id.required']);
        $this->assertEquals('User ID must be an integer', $messages['user_id.integer']);
        $this->assertEquals('User ID must exist in the users table', $messages['user_id.exists']);
    }

    /**
     * Test failed validation for the company request.
     *
     * @param array $invalidData
     * @param array $expectedErrors
     * @return void
     */
    #[DataProvider('invalidCompanyDataProvider')]
    public function testFailedValidation(array $invalidData, array $expectedErrors): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];

        $response = $this->postJson(self::COMPANIES_URL, $invalidData, [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($expectedErrors);
    }

    /**
     * Data provider for testFailedValidation.
     *
     * @return array
     */
    public static function invalidCompanyDataProvider(): array
    {
        return [
            [[], ['cnpj', 'legal_name', 'email', 'phone', 'address', 'size', 'user_id']],
            [['cnpj' => '123', 'legal_name' => '', 'email' => 'invalid-email', 'phone' => '', 'address' => '', 'size' => 'invalid', 'user_id' => ''], ['legal_name', 'email', 'phone', 'address', 'size', 'user_id']],
            [['cnpj' => '12345678901234', 'legal_name' => 'Test Legal Name', 'email' => 'test@example.com', 'phone' => '1234567890', 'address' => '1234 Test St.', 'size' => 'MEI', 'user_id' => 999], ['user_id']],
        ];
    }

    /**
     * Test successful validation for the company request.
     *
     * @return void
     */
    public function testSuccessfulValidation(): void
    {
        $authData = $this->authenticate();
        $token = $authData['token'];

        $user = $authData['user'];
        $validData = [
            'cnpj' => '12345678901234',
            'trade_name' => 'Test Trade Name',
            'legal_name' => 'Test Legal Name',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'address' => '1234 Test St.',
            'size' => 'MEI',
            'user_id' => $user->id,
        ];

        $response = $this->postJson(self::COMPANIES_URL, $validData, [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(201);
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
