<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ServiceRequestTest
 *
 * This class contains unit tests for the ServiceRequest model.
 * It tests the fillable attributes and relationships of the ServiceRequest model.
 *
 * @package Tests\Unit\Models
 */
class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $serviceRequest;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['user_id' => $this->user->id]);
        $this->serviceRequest = ServiceRequest::factory()->create(['company_id' => $this->company->id]);
    }

    /**
     * Test that the ServiceRequest model has the correct fillable attributes.
     *
     * @return void
     */
    public function testItHasFillableAttributes(): void
    {
        $serviceRequest = new ServiceRequest();

        $this->assertEquals([
            'title',
            'description',
            'status',
            'company_id',
        ], $serviceRequest->getFillable());
    }

    /**
     * Test that the ServiceRequest model belongs to a Company.
     *
     * @return void
     */
    public function testItBelongsToACompany(): void
    {
        $this->assertInstanceOf(BelongsTo::class, $this->serviceRequest->company());
        $this->assertTrue($this->serviceRequest->company->is($this->company));
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        $this->serviceRequest->delete();
        $this->company->delete();
        $this->user->delete();
        parent::tearDown();
    }
}
