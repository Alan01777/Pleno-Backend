<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Services\UserService;
use App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

/**
 * Class UserServiceTest
 *
 * This class contains unit tests for the UserService.
 * It tests the CRUD functionalities and various query methods of the UserService.
 *
 * @package Tests\Unit\Services
 */
class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $userService;
    protected $userRepository;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->userRepository);
    }

    /**
     * Test creating a user.
     *
     * @return void
     */
    public function testCreateUser(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password'
        ];

        $user = User::factory()->make($data);

        $this->userRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($user);

        $result = $this->userService->create($data);

        $this->assertEquals($data['name'], $result['name']);
        $this->assertEquals($data['email'], $result['email']);
    }

    /**
     * Test finding a user by ID.
     *
     * @return void
     */
    public function testFindUserById(): void
    {
        $user = User::factory()->make(['id' => 1]);

        $this->userRepository
            ->shouldReceive('findById')
            ->once()
            ->with($user->id)
            ->andReturn($user);

        $result = $this->userService->findById($user->id);

        $this->assertEquals($user->toArray(), $result);
    }

    public function testFindAllUsers(): void
    {
        $users = User::factory()->count(5)->make()->toArray();

        $this->userRepository->
            shouldReceive('findAll')->
            once()->
            andReturn($users);

        $result = $this->userService->findAll();

        $this->assertEquals($users, $result);
        $this->assertCount(5, $result);
    }

    /**
     * Test updating a user.
     *
     * @return void
     */
    public function testUpdateUser(): void
    {
        $user = User::factory()->make(['id' => 1]);
        $updateData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $this->userRepository
            ->shouldReceive('update')
            ->once()
            ->with($user->id, $updateData)
            ->andReturn(true);

        $this->userRepository
            ->shouldReceive('findById')
            ->once()
            ->with($user->id)
            ->andReturn($user);

        $result = $this->userService->update($user->id, $updateData);

        $this->assertEquals($user->toArray(), $result);
    }

    /**
     * Test deleting a user.
     *
     * @return void
     */
    public function testDeleteUser(): void
    {
        $user = User::factory()->make(['id' => 1]);

        $this->userRepository
            ->shouldReceive('delete')
            ->once()
            ->with($user->id)
            ->andReturn(true);

        $result = $this->userService->delete($user->id);

        $this->assertTrue($result);
    }

    /**
     * Test finding a user by email.
     *
     * @return void
     */
    public function testFindUserByEmail(): void
    {
        $user = User::factory()->make();

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($user->email)
            ->andReturn($user);

        $result = $this->userService->findByEmail($user->email);

        $this->assertEquals($user->toArray(), $result);
    }

    /**
     * Test finding a user by username.
     *
     * @return void
     */
    public function testFindUserByUsername(): void
    {
        $user = User::factory()->make();

        $this->userRepository
            ->shouldReceive('findByUsername')
            ->once()
            ->with($user->name)
            ->andReturn($user);

        $result = $this->userService->findByUsername($user->name);

        $this->assertEquals($user->toArray(), $result);
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
