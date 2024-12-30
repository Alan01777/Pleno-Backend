<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\User;
use App\Models\File;
use App\Models\ServiceRequest;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_it_has_fillable_attributes()
    {
        $company = new Company();

        $this->assertEquals([
            'cnpj',
            'legal_name',
            'trade_name',
            'address',
            'phone',
            'email',
            'size',
        ], $company->getFillable());
    }

    public function test_it_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->company->user);
        $this->assertEquals($this->user->id, $this->company->user->id);
    }

    public function test_it_has_many_files()
    {
        $file = File::factory()->create(['company_id' => $this->company->id]);

        $this->assertInstanceOf(File::class, $this->company->files->first());
        $this->assertEquals($file->id, $this->company->files->first()->id);
    }

    public function test_it_has_many_service_requests()
    {
        $serviceRequest = ServiceRequest::factory()->create(['company_id' => $this->company->id]);

        $this->assertInstanceOf(ServiceRequest::class, $this->company->serviceRequests->first());
        $this->assertEquals($serviceRequest->id, $this->company->serviceRequests->first()->id);
    }

    protected function tearDown(): void
    {
        $this->company->delete();
        $this->user->delete();
        parent::tearDown();
    }
}
