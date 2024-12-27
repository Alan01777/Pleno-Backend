<?php
namespace Tests\Unit\Models;

use App\Models\File;
use App\Models\Company;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;


class FileTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $file;
    protected $user;


    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['user_id' => $this->user->id]);
        $this->file = File::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_it_has_fillable_attributes()
    {
        $file = new File();

        $this->assertEquals([
            'name',
            'path',
            'extension',
            'mime_type',
            'size',
            'company_id',
        ], $file->getFillable());
    }

    public function test_it_belongs_to_a_company()
    {

        $this->assertInstanceOf(BelongsTo::class, $this->file->company());
        $this->assertTrue($this->file->company->is($this->company));
    }

    protected function tearDown(): void
    {
        $this->file->delete();
        $this->company->delete();
        $this->user->delete();
        parent::tearDown();
    }
}
