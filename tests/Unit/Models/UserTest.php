<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class UserTest
 *
 * This class contains unit tests for the User model.
 * It tests the fillable attributes, hidden attributes, casts, and relationships of the User model.
 *
 * @package Tests\Unit\Models
 */
class UserTest extends TestCase
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
     * Test that the User model has the correct fillable attributes.
     *
     * @return void
     */
    public function testItHasFillableAttributes(): void
    {
        $user = new User();

        $this->assertEquals([
            'name',
            'email',
            'password',
        ], $user->getFillable());
    }

    /**
     * Test that the User model has the correct hidden attributes.
     *
     * @return void
     */
    public function testItHasHiddenAttributes(): void
    {
        $user = new User();

        $this->assertEquals([
            'password',
            'remember_token',
        ], $user->getHidden());
    }

    /**
     * Test that the User model has the correct casts.
     *
     * @return void
     */
    public function testItHasCasts(): void
    {
        $user = new User();

        $this->assertEquals([
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'id' => 'int',
        ], $user->getCasts());
    }

    /**
     * Test that the User model has many Companies.
     *
     * @return void
     */
    public function testItHasManyCompanies(): void
    {
        $this->assertInstanceOf(HasMany::class, $this->user->companies());
        $this->assertTrue($this->user->companies->contains($this->company));
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
