<?php

namespace Tests\Feature\services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use \App\Repositories\UserRepository;
use App\Services\UserService;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $userService;
    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
    }

    public function test_create_user(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'laravel@email.com',
            'password' => 'password'
        ];

        $user = $this->userService->create($data);

        $this->assertEquals($data['name'], $user['name']);
        $this->assertEquals($data['email'], $user['email']);
    }

    public function test_find_user_by_id(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'laravel@email.com',
            'password' => 'password'
        ];

        $user = $this->userService->create($data);

        $foundUser = $this->userService->findById($user['id']);

        $this->assertEquals($data['name'], $foundUser['name']);
        $this->assertEquals($data['email'], $foundUser['email']);
    }


    public function test_update_user(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'laravel@email.com',
            'password' => 'password'
        ];

        $user = $this->userService->create($data);

        $updateData = [
            'name' => 'Jane Doe',
            'email' => 'laravelII@email.com',
        ];

        $this->userService->update($user['id'], $updateData);

        $updatedUser = $this->userService->findById($user['id']);

        $this->assertEquals($updateData['name'], $updatedUser['name']);
        $this->assertEquals($updateData['email'], $updatedUser['email']);
    }


    public function test_delete_user(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'laravel@email.com',
            'password' => 'password'
        ];

        $user = $this->userService->create($data);

        $this->userService->delete($user['id']);

        $this->assertNull($this->userService->findById($user['id']));
    }

    public function test_find_user_by_email(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'laravel@email.com',
            'password' => 'password'
        ];

        $user = $this->userService->create($data);

        $foundUser = $this->userService->findByEmail($data['email']);

        $this->assertEquals($data['name'], $foundUser['name']);
        $this->assertEquals($data['email'], $foundUser['email']);
    }


    public function test_find_user_by_username(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'laravel@email.com',
            'password' => 'password'
        ];

        $user = $this->userService->create($data);

        $foundUser = $this->userService->findByUsername($data['name']);

        $this->assertEquals($data['name'], $foundUser['name']);
        $this->assertEquals($data['email'], $foundUser['email']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->userRepository = null;
        $this->userService = null;
    }
}
