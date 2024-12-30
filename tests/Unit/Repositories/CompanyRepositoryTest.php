<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $companyRepository;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->companyRepository = new CompanyRepository();
        $this->user = User::factory()->create();
    }

    public function testCreateCompany()
    {
        $data = [
            'email' => 'test@example.com',
            'address' => 'Test Address',
            'cnpj' => '12345678901234',
            'trade_name' => 'Test Trade Name',
            'legal_name' => 'Test Legal Name',
            'phone' => '1234567890',
            'size' => 'MEI', // Ensure this value is valid
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

    public function testFindCompanyById()
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findById($company->id);

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals($company->id, $foundCompany->id);
    }

    public function testFindCompanyByIdNotFound()
    {
        $foundCompany = $this->companyRepository->findById(999);

        $this->assertNull($foundCompany);
    }

    public function testUpdateCompany()
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $data = ['legal_name' => 'Updated Legal Name'];
        $updated = $this->companyRepository->update($company->id, $data);

        $this->assertTrue($updated);
        $this->assertEquals('Updated Legal Name', $company->fresh()->legal_name);
    }

    public function testUpdateCompanyNotFound()
    {
        $data = ['legal_name' => 'Updated Legal Name'];
        $updated = $this->companyRepository->update(999, $data);

        $this->assertFalse($updated);
    }

    public function testDeleteCompany()
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $deleted = $this->companyRepository->delete($company->id);

        $this->assertTrue($deleted);
        $this->assertNull(Company::find($company->id));
    }

    public function testDeleteCompanyNotFound()
    {
        $deleted = $this->companyRepository->delete(999);

        $this->assertFalse($deleted);
    }

    public function testFindCompanyByEmail()
    {
        $company = Company::factory()->create(['email' => 'test@example.com', 'user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findByEmail('test@example.com');

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals('test@example.com', $foundCompany->email);
    }

    public function testFindCompanyByEmailNotFound()
    {
        $foundCompany = $this->companyRepository->findByEmail('nonexistent@example.com');

        $this->assertNull($foundCompany);
    }

    public function testFindCompanyByCnpj()
    {
        $company = Company::factory()->create(['cnpj' => '12345678901234', 'user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findByCnpj('12345678901234');

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals('12345678901234', $foundCompany->cnpj);
    }

    public function testFindCompanyByCnpjNotFound()
    {
        $foundCompany = $this->companyRepository->findByCnpj('00000000000000');

        $this->assertNull($foundCompany);
    }

    public function testFindCompanyByTradeName()
    {
        $company = Company::factory()->create(['trade_name' => 'Test Trade Name', 'user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findByTradeName('Test Trade Name');

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals('Test Trade Name', $foundCompany->trade_name);
    }

    public function testFindCompanyByTradeNameNotFound()
    {
        $foundCompany = $this->companyRepository->findByTradeName('Nonexistent Trade Name');

        $this->assertNull($foundCompany);
    }

    public function testFindCompanyByLegalName()
    {
        $company = Company::factory()->create(['legal_name' => 'Test Legal Name', 'user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findByLegalName('Test Legal Name');

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals('Test Legal Name', $foundCompany->legal_name);
    }

    public function testFindCompanyByLegalNameNotFound()
    {
        $foundCompany = $this->companyRepository->findByLegalName('Nonexistent Legal Name');

        $this->assertNull($foundCompany);
    }

    public function testFindCompanyByPhone()
    {
        $company = Company::factory()->create(['phone' => '1234567890', 'user_id' => $this->user->id]);

        $foundCompany = $this->companyRepository->findByPhone('1234567890');

        $this->assertInstanceOf(Company::class, $foundCompany);
        $this->assertEquals('1234567890', $foundCompany->phone);
    }

    public function testFindCompanyByPhoneNotFound()
    {
        $foundCompany = $this->companyRepository->findByPhone('0000000000');

        $this->assertNull($foundCompany);
    }

    public function testFindAllCompanies()
    {
        Company::factory()->count(3)->create(['user_id' => $this->user->id]);

        $companies = $this->companyRepository->findAll();

        $this->assertCount(3, $companies);
    }

    public function testFindAllCompaniesEmpty()
    {
        $companies = $this->companyRepository->findAll();

        $this->assertCount(0, $companies);
    }

    public function testFindCompaniesBySize()
    {
        Company::factory()->count(2)->create(['size' => 'MEI', 'user_id' => $this->user->id]);
        Company::factory()->count(3)->create(['size' => 'EG', 'user_id' => $this->user->id]);

        $meiCompanies = $this->companyRepository->findBySize('MEI');
        $egCompanies = $this->companyRepository->findBySize('EG');

        $this->assertCount(2, $meiCompanies);
        $this->assertCount(3, $egCompanies);
    }

    public function testFindCompaniesBySizeNotFound()
    {
        $companies = $this->companyRepository->findBySize('nonexistent');

        $this->assertCount(0, $companies);
    }
}
