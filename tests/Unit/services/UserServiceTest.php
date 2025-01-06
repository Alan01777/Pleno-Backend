<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Services\UserService;
use App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Exception;

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

        $this->userRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn($users);

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
     * Test handling an exception in the UserService.
     *
     * @return void
     */
    public function testHandleExceptionInCreate(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password'
        ];

        $this->userRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to create user.');

        $this->userService->create($data);
    }

    public function testHandleExceptionInFindById(): void
    {
        $userId = 1;

        $this->userRepository
            ->shouldReceive('findById')
            ->once()
            ->with($userId)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to find user.');

        $this->userService->findById($userId);
    }

    public function testHandleExceptionInFindAll(): void
    {
        $this->userRepository
            ->shouldReceive('findAll')
            ->once()
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to retrieve users.');

        $this->userService->findAll();
    }

    public function testHandleExceptionInUpdate(): void
    {
        $userId = 1;
        $updateData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $this->userRepository
            ->shouldReceive('update')
            ->once()
            ->with($userId, $updateData)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to update user.');

        $this->userService->update($userId, $updateData);
    }

    public function testHandleExceptionInDelete(): void
    {
        $userId = 1;

        $this->userRepository
            ->shouldReceive('delete')
            ->once()
            ->with($userId)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to delete user.');

        $this->userService->delete($userId);
    }

    public function testHandleExceptionInFindByEmail(): void
    {
        $email = 'john@example.com';

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to find user by email.');

        $this->userService->findByEmail($email);
    }

    public function testHandleExceptionInFindByUsername(): void
    {
        $username = 'JohnDoe';

        $this->userRepository
            ->shouldReceive('findByUsername')
            ->once()
            ->with($username)
            ->andThrow(new Exception('Test exception'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to find user by username.');

        $this->userService->findByUsername($username);
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
