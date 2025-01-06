<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;
use App\Contracts\Services\CompanyServiceInterface;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class CompanyControllerTest
 *
 * This class contains feature tests for the CompanyController.
 * It tests the CRUD functionalities for companies.
 *
 * @package Tests\Feature\Http\Controllers
 */
class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and get the JWT token
        $this->user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->token = $this->getTokenForUser([
            'email' => 'john@example.com',
            'password' => 'password',
        ]);
    }

    /**
     * Get the JWT token for a user.
     *
     * @param array $userData
     * @return string
     */
    protected function getTokenForUser(array $userData): string
    {
        $response = $this->postJson('/api/login', $userData);
        return $response->json('token');
    }

    /**
     * Data provider for company data.
     *
     * @return array
     */
    public static function companyDataProvider(): array
    {
        return [
            'valid company data' => [
                [
                    'legal_name' => 'Company Inc.',
                    'trade_name' => 'Company',
                    'email' => 'company@gmail.com',
                    'phone' => '1234567890',
                    'address' => '1234 Company St.',
                    'cnpj' => '12345678901234',
                    'size' => 'MEI',
                ],
            ],
            'invalid company data' => [
                [
                    'legal_name' => 'Company Inc.',
                    'trade_name' => 'Company',
                    'email' => 'company-email.com',
                    'phone' => '1234567890',
                    'address' => '1234 Company St.',
                    'cnpj' => '12345678901234',
                    'size' => 'Invalid',
                ],
            ],
        ];
    }

    /**
     * Test creating a company.
     *
     * @dataProvider companyDataProvider
     * @param array $companyData
     * @return void
     */
    #[DataProvider('companyDataProvider')]
    public function testCreateCompany(array $companyData): void
    {
        // Add user_id to company data
        $companyData['user_id'] = $this->user->id;

        $response = $this->postJson('/api/companies', $companyData, ['Authorization' => "Bearer {$this->token}"]);

        if (isset($companyData['size']) && $companyData['size'] === 'Invalid') {
            $response->assertStatus(422);
        } else {
            $response->assertStatus(201)
                ->assertJson(
                    fn(AssertableJson $json) =>
                    $json->where('legal_name', $companyData['legal_name'])
                        ->where('trade_name', $companyData['trade_name'])
                        ->where('email', $companyData['email'])
                        ->where('phone', $companyData['phone'])
                        ->where('address', $companyData['address'])
                        ->where('cnpj', $companyData['cnpj'])
                        ->where('size', $companyData['size'])
                        ->where('user_id', $companyData['user_id'])
                        ->etc()
                );
        }
    }

    /**
     * Test showing a company.
     *
     * @return void
     */
    public function testShowCompany(): void
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/companies/{$company->id}", ['Authorization' => "Bearer {$this->token}"]);

        $response->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->where('id', $company->id)
                    ->where('legal_name', $company->legal_name)
                    ->where('trade_name', $company->trade_name)
                    ->where('email', $company->email)
                    ->where('phone', $company->phone)
                    ->where('address', $company->address)
                    ->where('cnpj', $company->cnpj)
                    ->where('size', $company->size)
                    ->where('user_id', $company->user_id)
                    ->etc()
            );
    }

    /**
     * Test updating a company.
     *
     * @return void
     */
    public function testUpdateCompany(): void
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $updateData = ['legal_name' => 'Updated Legal Name'];

        $response = $this->putJson("/api/companies/{$company->id}", $updateData, ['Authorization' => "Bearer {$this->token}"]);

        $response->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->where('id', $company->id)
                    ->where('legal_name', 'Updated Legal Name')
                    ->etc()
            );

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'legal_name' => 'Updated Legal Name',
        ]);
    }

    /**
     * Test deleting a company.
     *
     * @return void
     */
    public function testDeleteCompany(): void
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/companies/{$company->id}", [], ['Authorization' => "Bearer {$this->token}"]);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    /**
     * Test listing all companies.
     *
     * @return void
     */
    public function testListCompanies(): void
    {
        Company::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/companies', ['Authorization' => "Bearer {$this->token}"]);

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test listing companies without a token.
     *
     * @return void
     */
    public function testListCompaniesWithoutToken(): void
    {
        $response = $this->getJson('/api/companies');

        $response->assertStatus(401);
    }

    /**
     * Test handling an exception in the CompanyController.
     *
     * @return void
     */
    public function testHandleException(): void
    {
        // Simulate an exception in the CompanyService
        $this->app->instance(CompanyServiceInterface::class, new class implements CompanyServiceInterface {
            public function create(array $data): array { throw new Exception('Test exception'); }
            public function findAll(): array { throw new Exception('Test exception'); }
            public function findById(int $id): array | null { throw new Exception('Test exception'); }
            public function update(int $id, array $data): array { throw new Exception('Test exception'); }
            public function delete(int $id): bool { throw new Exception('Test exception'); }
            public function findByEmail(string $email): array | null { throw new Exception('Test exception'); }
            public function findByCnpj(string $cnpj): array | null { throw new Exception('Test exception'); }
            public function findByTradeName(string $tradeName): array | null { throw new Exception('Test exception'); }
            public function findByLegalName(string $legalName): array | null { throw new Exception('Test exception'); }
            public function findByPhone(string $phone): array | null { throw new Exception('Test exception'); }
            public function findBySize(string $size): array | null { throw new Exception('Test exception'); }
            public function findAllByUserId(): array { throw new Exception('Test exception'); }
        });

        $response = $this->postJson('/api/companies', [
            'legal_name' => 'Company Inc.',
            'trade_name' => 'Company',
            'email' => 'company@gmail.com',
            'phone' => '1234567890',
            'address' => '1234 Company St.',
            'cnpj' => '12345678901234',
            'size' => 'MEI',
            'user_id' => $this->user->id,
        ], ['Authorization' => "Bearer {$this->token}"]);

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Test exception']);
    }

    /**
     * Test handling a NotFoundHttpException in the CompanyController.
     *
     * @return void
     */
    public function testHandleNotFoundHttpException(): void
    {
        // Simulate a NotFoundHttpException in the CompanyService
        $this->app->instance(CompanyServiceInterface::class, new class implements CompanyServiceInterface {
            public function create(array $data): array { throw new NotFoundHttpException('Company not found.'); }
            public function findAll(): array { throw new NotFoundHttpException('Company not found.'); }
            public function findById(int $id): array | null { throw new NotFoundHttpException('Company not found.'); }
            public function update(int $id, array $data): array { throw new NotFoundHttpException('Company not found.'); }
            public function delete(int $id): bool { throw new NotFoundHttpException('Company not found.'); }
            public function findByEmail(string $email): array | null { throw new NotFoundHttpException('Company not found.'); }
            public function findByCnpj(string $cnpj): array | null { throw new NotFoundHttpException('Company not found.'); }
            public function findByTradeName(string $tradeName): array | null { throw new NotFoundHttpException('Company not found.'); }
            public function findByLegalName(string $legalName): array | null { throw new NotFoundHttpException('Company not found.'); }
            public function findByPhone(string $phone): array | null { throw new NotFoundHttpException('Company not found.'); }
            public function findBySize(string $size): array | null { throw new NotFoundHttpException('Company not found.'); }
            public function findAllByUserId(): array { throw new NotFoundHttpException('Company not found.'); }
        });

        $response = $this->getJson('/api/companies/999', ['Authorization' => "Bearer {$this->token}"]);

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Company not found.']);
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
