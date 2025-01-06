<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Mockery;
use Exception;

/**
 * Class AuthServiceTest
 *
 * This class contains unit tests for the AuthService.
 * It tests the login and logout functionalities of the AuthService.
 *
 * @package Tests\Unit\Services
 */
class AuthServiceTest extends TestCase
{
    protected $authService;
    protected $userRepository;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->authService = new AuthService($this->userRepository);
    }

    /**
     * Test user login with valid credentials.
     *
     * @return void
     */
    public function testLoginWithValidCredentials(): void
    {
        $data = [
            'email' => 'john@example.com',
            'password' => 'password'
        ];

        $user = Mockery::mock(User::class)->makePartial();
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($data['email'])
            ->andReturn($user);

        $user->shouldReceive('createToken')
            ->once()
            ->andReturn((object)['plainTextToken' => 'test_token']);

        $response = $this->authService->login($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $response->getData(true));
    }

    /**
     * Test user login with invalid credentials.
     *
     * @return void
     */
    public function testLoginWithInvalidCredentials(): void
    {
        $data = [
            'email' => 'john@example.com',
            'password' => 'invalid_password'
        ];

        $user = User::factory()->make(['email' => $data['email'], 'password' => Hash::make('password')]);

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($data['email'])
            ->andReturn($user);

        $response = $this->authService->login($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(__('auth.failed'), $response->getData(true)['message']);
    }

    /**
     * Test user login with nonexistent email.
     *
     * @return void
     */
    public function testLoginWithNonexistentEmail(): void
    {
        $data = [
            'email' => 'nonexistent@example.com',
            'password' => 'password'
        ];

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($data['email'])
            ->andReturn(null);

        $response = $this->authService->login($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(__('auth.failed'), $response->getData(true)['message']);
    }

    /**
     * Test handling an exception during login.
     *
     * @return void
     */
    public function testHandleExceptionInLogin(): void
    {
        $data = [
            'email' => 'john@example.com',
            'password' => 'password'
        ];

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($data['email'])
            ->andThrow(new Exception('Test exception'));

        $response = $this->authService->login($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Login failed. Please try again later.', $response->getData(true)['message']);
    }

    /**
     * Test user logout.
     *
     * @return void
     */
    public function testLogout(): void
    {
        $user = User::factory()->make();

        Auth::shouldReceive('user')->once()->andReturn($user);
        $this->userRepository->shouldReceive('deleteTokens')->once()->with($user);

        $response = $this->authService->logout();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(__('auth.logged_out'), $response->getData(true)['message']);
    }

    /**
     * Test handling an exception during logout.
     *
     * @return void
     */
    public function testHandleExceptionInLogout(): void
    {
        $user = User::factory()->make();

        Auth::shouldReceive('user')->once()->andReturn($user);
        $this->userRepository->shouldReceive('deleteTokens')->once()->with($user)->andThrow(new Exception('Test exception'));

        $response = $this->authService->logout();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Logout failed. Please try again later.', $response->getData(true)['message']);
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
