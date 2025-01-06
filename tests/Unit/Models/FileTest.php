<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class FileTest
 *
 * This class contains unit tests for the File model.
 * It tests the relationships of the File model.
 *
 * @package Tests\Unit\Models
 */
class FileTest extends TestCase
{
    use RefreshDatabase;

    protected $file;
    protected $user;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->file = File::factory()->create(['user_id' => $this->user->id]);
    }

    /**
     * Test that the File model belongs to a User.
     *
     * @return void
     */
    public function testItBelongsToUser(): void
    {
        $this->assertInstanceOf(BelongsTo::class, $this->file->user());
        $this->assertEquals($this->user->id, $this->file->user->id);
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        $this->file->delete();
        $this->user->delete();
        parent::tearDown();
    }
}
