<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\User;
use App\Models\File;
use App\Models\ServiceRequest;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class CompanyTest
 *
 * This class contains unit tests for the Company model.
 * It tests the fillable attributes and relationships of the Company model.
 *
 * @package Tests\Unit\Models
 */
class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['user_id' => $this->user->id]);
    }

    /**
     * Test that the Company model has the correct fillable attributes.
     *
     * @return void
     */
    public function testItHasFillableAttributes(): void
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
            'user_id'
        ], $company->getFillable());
    }

    /**
     * Test that the Company model belongs to a User.
     *
     * @return void
     */
    public function testItBelongsToAUser(): void
    {
        $this->assertInstanceOf(User::class, $this->company->user);
        $this->assertEquals($this->user->id, $this->company->user->id);
    }

    /**
     * Test that the Company model has many Files.
     *
     * @return void
     */
    public function testItHasManyFiles(): void
    {
        $file = File::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id]);

        $this->assertInstanceOf(File::class, $this->company->files->first());
        $this->assertEquals($file->id, $this->company->files->first()->id);
    }

    /**
     * Test that the Company model has many Service Requests.
     *
     * @return void
     */
    public function testItHasManyServiceRequests(): void
    {
        $serviceRequest = ServiceRequest::factory()->create(['company_id' => $this->company->id]);

        $this->assertInstanceOf(ServiceRequest::class, $this->company->serviceRequests->first());
        $this->assertEquals($serviceRequest->id, $this->company->serviceRequests->first()->id);
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        $this->company->delete();
        $this->user->delete();
        parent::tearDown();
    }
}
