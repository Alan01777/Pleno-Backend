<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
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

    public function test_it_has_fillable(): void
    {
        $user = new User();

        $this->assertEquals([
            'name',
            'email',
            'password',
        ], $user->getFillable());
    }

    public function test_it_has_hidden(): void
    {
        $user = new User();

        $this->assertEquals([
            'password',
            'remember_token',
        ], $user->getHidden());
    }

    public function test_it_has_casts(): void
    {
        $user = new User();

        $this->assertEquals([
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'id' => 'int',
        ], $user->getCasts());
    }

    public function test_it_has_many_companies(): void
    {
        $this->assertInstanceOf(Company::class, $this->user->companies->first());
        $this->assertEquals($this->company->id, $this->user->companies->first()->id);
    }

    protected function tearDown(): void
    {
        $this->company->delete();
        $this->user->delete();
        parent::tearDown();
    }
}
