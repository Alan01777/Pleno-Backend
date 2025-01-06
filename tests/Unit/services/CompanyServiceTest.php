<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use App\Services\CompanyService;
use App\Contracts\Repositories\CompanyRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Exception;

/**
 * Class CompanyServiceTest
 *
 * This class contains unit tests for the CompanyService.
 * It tests the CRUD functionalities and various query methods of the CompanyService.
 *
 * @package Tests\Unit\Services
 */
class CompanyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CompanyService $companyService;
    protected CompanyRepositoryInterface $companyRepository;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyRepository = Mockery::mock(CompanyRepositoryInterface::class);
        $this->companyService = new CompanyService($this->companyRepository);
    }

    /**
     * Test finding all companies.
     *
     * @return void
     */
    public function testFindAll(): void
    {
        $companies = Company::factory()->count(3)->make()->toArray();

        $this->companyRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn($companies);

        $result = $this->companyService->findAll();

        $this->assertCount(3, $result);
        $this->assertEquals($companies, $result);
    }

    /**
     * Test creating a company.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $companyData = Company::factory()->make()->toArray();
        $company = Company::factory()->make($companyData);

        $this->companyRepository
            ->shouldReceive('create')
            ->once()
            ->with($companyData)
            ->andReturn($company);

        $result = $this->companyService->create($companyData);

        $this->assertEquals($company->toArray(), $result);
    }

    /**
     * Test finding a company by ID.
     *
     * @return void
     */
    public function testFindById(): void
    {
        $company = Company::factory()->make(['id' => 1]);

        $this->companyRepository
            ->shouldReceive('findById')
            ->once()
            ->with($company->id)
            ->andReturn($company);

        $result = $this->companyService->findById($company->id);

        $this->assertEquals($company->toArray(), $result);
    }

    /**
     * Test updating a company.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $company = Company::factory()->make(['id' => 1]);
        $updateData = ['legal_name' => 'Updated Legal Name'];

        $this->companyRepository
            ->shouldReceive('update')
            ->once()
            ->with($company->id, $updateData)
            ->andReturn(true);

        $this->companyRepository
            ->shouldReceive('findById')
            ->once()
            ->with($company->id)
            ->andReturn($company);

        $result = $this->companyService->update($company->id, $updateData);

        $this->assertEquals($company->toArray(), $result);
    }

    /**
     * Test deleting a company.
     *
     * @return void
     */
    public function testDelete(): void
    {
        $company = Company::factory()->make(['id' => 1]);

        $this->companyRepository
            ->shouldReceive('delete')
            ->once()
            ->with($company->id)
            ->andReturn(true);

        $result = $this->companyService->delete($company->id);

        $this->assertTrue($result);
    }

    /**
     * Test finding a company by email.
     *
     * @return void
     */
    public function testFindByEmail(): void
    {
        $company = Company::factory()->make();

        $this->companyRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($company->email)
            ->andReturn($company);

        $result = $this->companyService->findByEmail($company->email);

        $this->assertEquals($company->toArray(), $result);
    }

    /**
     * Test finding a company by CNPJ.
     *
     * @return void
     */
    public function testFindByCnpj(): void
    {
        $company = Company::factory()->make();

        $this->companyRepository
            ->shouldReceive('findByCnpj')
            ->once()
            ->with($company->cnpj)
            ->andReturn($company);

        $result = $this->companyService->findByCnpj($company->cnpj);

        $this->assertEquals($company->toArray(), $result);
    }

    /**
     * Test finding a company by trade name.
     *
     * @return void
     */
    public function testFindByTradeName(): void
    {
        $company = Company::factory()->make();

        $this->companyRepository
            ->shouldReceive('findByTradeName')
            ->once()
            ->with($company->trade_name)
            ->andReturn($company);

        $result = $this->companyService->findByTradeName($company->trade_name);

        $this->assertEquals($company->toArray(), $result);
    }

    /**
     * Test finding a company by legal name.
     *
     * @return void
     */
    public function testFindByLegalName(): void
    {
        $company = Company::factory()->make();

        $this->companyRepository
            ->shouldReceive('findByLegalName')
            ->once()
            ->with($company->legal_name)
            ->andReturn($company);

        $result = $this->companyService->findByLegalName($company->legal_name);

        $this->assertEquals($company->toArray(), $result);
    }

    /**
     * Test finding a company by phone.
     *
     * @return void
     */
    public function testFindByPhone(): void
    {
        $company = Company::factory()->make();

        $this->companyRepository
            ->shouldReceive('findByPhone')
            ->once()
            ->with($company->phone)
            ->andReturn($company);

        $result = $this->companyService->findByPhone($company->phone);

        $this->assertEquals($company->toArray(), $result);
    }

    /**
     * Test finding companies by size.
     *
     * @return void
     */
    public function testFindBySize(): void
    {
        $companies = Company::factory()->count(3)->make(['size' => 'MEI'])->toArray();

        $this->companyRepository
            ->shouldReceive('findBySize')
            ->once()
            ->with('MEI')
            ->andReturn($companies);

        $result = $this->companyService->findBySize('MEI');

        $this->assertCount(3, $result);
        $this->assertEquals($companies, $result);
    }

    /**
     * Test finding all companies by user ID.
     *
     * @return void
     */
    public function testFindAllByUserId(): void
    {
        $userId = 1;
        $companies = Company::factory()->count(3)->make(['user_id' => $userId])->toArray();

        $this->companyRepository
            ->shouldReceive('findAllByUserId')
            ->once()
            ->with($userId)
            ->andReturn($companies);

        $this->actingAs(User::factory()->make(['id' => $userId]));

        $result = $this->companyService->findAllByUserId();

        $this->assertCount(3, $result);
        $this->assertEquals($companies, $result);
    }

    /**
     * Test handling an exception in the CompanyService.
     *
     * @return void
     */
    public function testHandleExceptionInFindAll(): void
    {
        $this->companyRepository
            ->shouldReceive('findAll')
            ->once()
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to retrieve companies.');

        $this->companyService->findAll();
    }

    public function testHandleExceptionInCreate(): void
    {
        $companyData = Company::factory()->make()->toArray();

        $this->companyRepository
            ->shouldReceive('create')
            ->once()
            ->with($companyData)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to create company.');

        $this->companyService->create($companyData);
    }

    public function testHandleExceptionInFindById(): void
    {
        $companyId = 1;

        $this->companyRepository
            ->shouldReceive('findById')
            ->once()
            ->with($companyId)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to find company.');

        $this->companyService->findById($companyId);
    }

    public function testHandleExceptionInUpdate(): void
    {
        $companyId = 1;
        $updateData = ['legal_name' => 'Updated Legal Name'];

        $this->companyRepository
            ->shouldReceive('update')
            ->once()
            ->with($companyId, $updateData)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to update company.');

        $this->companyService->update($companyId, $updateData);
    }

    public function testHandleExceptionInDelete(): void
    {
        $companyId = 1;

        $this->companyRepository
            ->shouldReceive('delete')
            ->once()
            ->with($companyId)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to delete company.');

        $this->companyService->delete($companyId);
    }

    public function testHandleExceptionInFindByEmail(): void
    {
        $email = 'company@example.com';

        $this->companyRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to find company by email.');

        $this->companyService->findByEmail($email);
    }

    public function testHandleExceptionInFindByCnpj(): void
    {
        $cnpj = '12345678901234';

        $this->companyRepository
            ->shouldReceive('findByCnpj')
            ->once()
            ->with($cnpj)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to find company by CNPJ.');

        $this->companyService->findByCnpj($cnpj);
    }

    public function testHandleExceptionInFindByTradeName(): void
    {
        $tradeName = 'Company';

        $this->companyRepository
            ->shouldReceive('findByTradeName')
            ->once()
            ->with($tradeName)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to find company by trade name.');

        $this->companyService->findByTradeName($tradeName);
    }

    public function testHandleExceptionInFindByLegalName(): void
    {
        $legalName = 'Company Inc.';

        $this->companyRepository
            ->shouldReceive('findByLegalName')
            ->once()
            ->with($legalName)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to find company by legal name.');

        $this->companyService->findByLegalName($legalName);
    }

    public function testHandleExceptionInFindByPhone(): void
    {
        $phone = '1234567890';

        $this->companyRepository
            ->shouldReceive('findByPhone')
            ->once()
            ->with($phone)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to find company by phone.');

        $this->companyService->findByPhone($phone);
    }

    public function testHandleExceptionInFindBySize(): void
    {
        $size = 'MEI';

        $this->companyRepository
            ->shouldReceive('findBySize')
            ->once()
            ->with($size)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to find company by size.');

        $this->companyService->findBySize($size);
    }

    public function testHandleExceptionInFindAllByUserId(): void
    {
        $userId = 1;

        $this->companyRepository
            ->shouldReceive('findAllByUserId')
            ->once()
            ->with($userId)
            ->andThrow(new Exception('Test exception'));

        $this->actingAs(User::factory()->make(['id' => $userId]));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to find companies by user ID.');

        $this->companyService->findAllByUserId();
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
