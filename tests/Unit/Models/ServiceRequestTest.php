<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\ServiceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $serviceRequest;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['user_id' => $this->user->id]);
        $this->serviceRequest = ServiceRequest::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_it_has_fillable_attributes()
    {
        $serviceRequest = new ServiceRequest();

        $this->assertEquals([
            'title',
            'description',
            'status',
            'company_id',
        ], $serviceRequest->getFillable());
    }

    public function test_it_belongs_to_a_company()
    {

        $this->assertInstanceOf(Company::class, $this->serviceRequest->company);
        $this->assertEquals($this->company->id, $this->serviceRequest->company->id);
    }

    protected function tearDown(): void
    {
        $this->serviceRequest->delete();
        $this->company->delete();
        $this->user->delete();
        parent::tearDown();
    }
}
