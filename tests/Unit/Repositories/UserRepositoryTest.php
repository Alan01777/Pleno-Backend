<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class UserRepositoryTest
 *
 * This class contains unit tests for the UserRepository.
 * It tests the CRUD functionalities and various query methods of the UserRepository.
 *
 * @package Tests\Unit\Repositories
 */
class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $userRepository;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
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
            'password' => bcrypt('password'),
        ];

        $user = $this->userRepository->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
    }

    /**
     * Test finding a user by ID.
     *
     * @return void
     */
    public function testFindUserById(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ];

        $user = $this->userRepository->create($data);

        $foundUser = $this->userRepository->findById($user->id);

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('John Doe', $foundUser->name);
    }

    /**
     * Test finding all users.
     *
     * @return void
     */
    public function testFindAllUsers(): void
    {
        User::factory()->count(5)->create();

        $users = $this->userRepository->findAll();

        $this->assertCount(5, $users);
    }

    /**
     * Test finding a user with an invalid ID.
     *
     * @return void
     */
    public function testFindUserWithInvalidId(): void
    {
        $result = $this->userRepository->findById(0);

        $this->assertNull($result);
    }

    /**
     * Test updating a user.
     *
     * @return void
     */
    public function testUpdateUser(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ];

        $user = $this->userRepository->create($data);

        $updateData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $this->userRepository->update($user->id, $updateData);

        $updatedUser = $this->userRepository->findById($user->id);

        $this->assertEquals('Jane Doe', $updatedUser->name);
    }

    /**
     * Test updating a user with an invalid ID.
     *
     * @return void
     */
    public function testUpdateUserWithInvalidId(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ];

        $this->userRepository->create($data);

        $updateData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $result = $this->userRepository->update(0, $updateData);

        $this->assertFalse($result);
    }

    /**
     * Test deleting a user.
     *
     * @return void
     */
    public function testDeleteUser(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ];

        $user = $this->userRepository->create($data);

        $this->userRepository->delete($user->id);

        $deletedUser = $this->userRepository->findById($user->id);

        $this->assertNull($deletedUser);
    }

    /**
     * Test deleting a user with an invalid ID.
     *
     * @return void
     */
    public function testDeleteUserWithInvalidId(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ];

        $this->userRepository->create($data);

        $result = $this->userRepository->delete(0);

        $this->assertFalse($result);
    }

    /**
     * Test finding a user by username.
     *
     * @return void
     */
    public function testFindUserByUsername(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ];

        $user = $this->userRepository->create($data);

        $foundUser = $this->userRepository->findByUsername('John Doe');

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('John Doe', $foundUser->name);
    }

    /**
     * Test finding a user by email.
     *
     * @return void
     */
    public function testFindUserByEmail(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ];

        $user = $this->userRepository->create($data);

        $foundUser = $this->userRepository->findByEmail('john@example.com');

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('john@example.com', $foundUser->email);
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->userRepository = null;
    }
}
