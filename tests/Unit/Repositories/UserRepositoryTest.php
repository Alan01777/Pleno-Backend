<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
    }

    public function test_create_user(): void
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

    public function test_find_user_by_id(): void
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

    public function test_find_user_with_invalid_id(): void
    {
        $result = $this->userRepository->findById(0);

        $this->assertNull($result);
    }

    public function test_update_user(): void
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

    public function test_update_user_with_invalid_id(): void
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

    public function test_delete_user(): void
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

    public function test_delete_user_with_invalid_id(): void
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

    public function test_find_user_by_username(): void
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

    public function test_find_user_by_email(): void
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

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->userRepository = null;
    }
}
