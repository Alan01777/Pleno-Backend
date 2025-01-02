<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class CompanyRepositoryTest
 *
 * This class contains unit tests for the CompanyRepository.
 * It tests the CRUD functionalities and various query methods of the CompanyRepository.
 *
 * @package Tests\Unit
 */
class CompanyRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $companyRepository;
    protected $user;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->companyRepository = new CompanyRepository();
        $this->user = User::factory()->create();
    }

    /**
     * Test creating a company.
     *
     * @return void
     */
    public function testCreateCompany(): void
    {
        $data = [
            'email' => 'test@example.com',
            'address' => 'Test Address',
            'cnpj' => '12345678901234',
            'trade_name' => 'Test Trade Name',
            'legal_name' => 'Test Legal Name',
            'phone' => '1234567890',
            'size' => 'MEI',
            'user_id' => $this->user->id
        ];

        $company = $this->companyRepository->create($data);

        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals('test@example.com', $company->email);
        $this->assertEquals('12345678901234', $company->cnpj);
        $this->assertEquals('Test Trade Name', $company->trade_name);
        $this->assertEquals('Test Legal Name', $company->legal_name);
        $this->assertEquals('1234567890', $company->phone);
        $this->assertEquals('MEI', $company->size);
        $this->assertEquals($this->user->id, $company->user_id);
    }

    /**
     * Test finding a company by ID.
     *
     * @return void
     */
    public function testFindCompanyById(): void
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findById($company->id);

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals($company->id, $foundCompany->id);
    }

    /**
     * Test finding a company by ID that does not exist.
     *
     * @return void
     */
    public function testFindCompanyByIdNotFound(): void
    {
        $foundCompany = $this->companyRepository->findById(999);

        $this->assertNull($foundCompany);
    }

    /**
     * Test updating a company.
     *
     * @return void
     */
    public function testUpdateCompany(): void
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $data = ['legal_name' => 'Updated Legal Name'];
        $updated = $this->companyRepository->update($company->id, $data);

        $this->assertTrue($updated);
        $this->assertEquals('Updated Legal Name', $company->fresh()->legal_name);
    }

    /**
     * Test updating a company that does not exist.
     *
     * @return void
     */
    public function testUpdateCompanyNotFound(): void
    {
        $data = ['legal_name' => 'Updated Legal Name'];
        $updated = $this->companyRepository->update(999, $data);

        $this->assertFalse($updated);
    }

    /**
     * Test deleting a company.
     *
     * @return void
     */
    public function testDeleteCompany(): void
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $deleted = $this->companyRepository->delete($company->id);

        $this->assertTrue($deleted);
        $this->assertNull(Company::find($company->id));
    }

    /**
     * Test deleting a company that does not exist.
     *
     * @return void
     */
    public function testDeleteCompanyNotFound(): void
    {
        $deleted = $this->companyRepository->delete(999);

        $this->assertFalse($deleted);
    }

    /**
     * Test finding a company by email.
     *
     * @return void
     */
    public function testFindCompanyByEmail(): void
    {
        $company = Company::factory()->create(['email' => 'test@example.com', 'user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findByEmail('test@example.com');

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals('test@example.com', $foundCompany->email);
    }

    /**
     * Test finding a company by email that does not exist.
     *
     * @return void
     */
    public function testFindCompanyByEmailNotFound(): void
    {
        $foundCompany = $this->companyRepository->findByEmail('nonexistent@example.com');

        $this->assertNull($foundCompany);
    }

    /**
     * Test finding a company by CNPJ.
     *
     * @return void
     */
    public function testFindCompanyByCnpj(): void
    {
        $company = Company::factory()->create(['cnpj' => '12345678901234', 'user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findByCnpj('12345678901234');

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals('12345678901234', $foundCompany->cnpj);
    }

    /**
     * Test finding a company by CNPJ that does not exist.
     *
     * @return void
     */
    public function testFindCompanyByCnpjNotFound(): void
    {
        $foundCompany = $this->companyRepository->findByCnpj('00000000000000');

        $this->assertNull($foundCompany);
    }

    /**
     * Test finding a company by trade name.
     *
     * @return void
     */
    public function testFindCompanyByTradeName(): void
    {
        $company = Company::factory()->create(['trade_name' => 'Test Trade Name', 'user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findByTradeName('Test Trade Name');

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals('Test Trade Name', $foundCompany->trade_name);
    }

    /**
     * Test finding a company by trade name that does not exist.
     *
     * @return void
     */
    public function testFindCompanyByTradeNameNotFound(): void
    {
        $foundCompany = $this->companyRepository->findByTradeName('Nonexistent Trade Name');

        $this->assertNull($foundCompany);
    }

    /**
     * Test finding a company by legal name.
     *
     * @return void
     */
    public function testFindCompanyByLegalName(): void
    {
        $company = Company::factory()->create(['legal_name' => 'Test Legal Name', 'user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findByLegalName('Test Legal Name');

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals('Test Legal Name', $foundCompany->legal_name);
    }

    /**
     * Test finding a company by legal name that does not exist.
     *
     * @return void
     */
    public function testFindCompanyByLegalNameNotFound(): void
    {
        $foundCompany = $this->companyRepository->findByLegalName('Nonexistent Legal Name');

        $this->assertNull($foundCompany);
    }

    /**
     * Test finding a company by phone.
     *
     * @return void
     */
    public function testFindCompanyByPhone(): void
    {
        $company = Company::factory()->create(['phone' => '1234567890', 'user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findByPhone('1234567890');

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals('1234567890', $foundCompany->phone);
    }

    /**
     * Test finding a company by phone that does not exist.
     *
     * @return void
     */
    public function testFindCompanyByPhoneNotFound(): void
    {
        $foundCompany = $this->companyRepository->findByPhone('0000000000');

        $this->assertNull($foundCompany);
    }

    /**
     * Test finding all companies.
     *
     * @return void
     */
    public function testFindAllCompanies(): void
    {
        Company::factory()->count(3)->create(['user_id' => $this->user->id]);

        $companies = $this->companyRepository->findAll();

        $this->assertCount(3, $companies);
    }

    /**
     * Test finding all companies when there are none.
     *
     * @return void
     */
    public function testFindAllCompaniesEmpty(): void
    {
        $companies = $this->companyRepository->findAll();

        $this->assertCount(0, $companies);
    }

    /**
     * Test finding companies by size.
     *
     * @return void
     */
    public function testFindCompaniesBySize(): void
    {
        Company::factory()->count(2)->create(['size' => 'MEI', 'user_id' => $this->user->id]);
        Company::factory()->count(3)->create(['size' => 'EG', 'user_id' => $this->user->id]);

        $meiCompanies = $this->companyRepository->findBySize('MEI');
        $egCompanies = $this->companyRepository->findBySize('EG');

        $this->assertCount(2, $meiCompanies);
        $this->assertCount(3, $egCompanies);
    }

    /**
     * Test finding companies by size that does not exist.
     *
     * @return void
     */
    public function testFindCompaniesBySizeNotFound(): void
    {
        $companies = $this->companyRepository->findBySize('nonexistent');

        $this->assertCount(0, $companies);
    }
}
