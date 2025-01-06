<?php

namespace Tests\Unit\Models;

use App\Models\File;
use App\Models\Company;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class FileTest
 *
 * This class contains unit tests for the File model.
 * It tests the fillable attributes and relationships of the File model.
 *
 * @package Tests\Unit\Models
 */
class FileTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $file;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['user_id' => $this->user->id]);
        $this->file = File::factory()->create(['company_id' => $this->company->id]);
    }

    /**
     * Test that the File model has the correct fillable attributes.
     *
     * @return void
     */
    public function testItHasFillableAttributes(): void
    {
        $file = new File([
            'name' => 'example.txt',
            'hash_name' => 'example_hash.txt',
            'path' => 'files/example.txt',
            'mime_type' => 'text/plain',
            'size' => 12345,
            'company_id' => 1,
            'user_id' => 1,
        ]);

        $this->assertEquals([
            'name',
            'hash_name',
            'path',
            'mime_type',
            'size',
            'company_id',
            'user_id',
        ], $file->getFillable());
    }

    /**
     * Test that the File model belongs to a Company.
     *
     * @return void
     */
    public function testItBelongsToACompany(): void
    {
        $this->assertInstanceOf(BelongsTo::class, $this->file->company());
        $this->assertTrue($this->file->company->is($this->company));
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        $this->file->delete();
        $this->company->delete();
        $this->user->delete();
        parent::tearDown();
    }
}
