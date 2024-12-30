<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\UserRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create([
            'email' => 'auth@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'auth@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        return [
            'token' => $response->json('token'),
            'user' => $user
        ];
    }

    public function test_authorize()
    {
        $request = new UserRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_rules_for_post_method()
    {
        // Create a new request instance with the POST method
        $request = UserRequest::create('/api/users', 'POST');

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertEquals('required|string', $rules['name']);
        $this->assertEquals('required|email|unique:users,email', $rules['email']);
        $this->assertEquals('required|string|min:6', $rules['password']);
    }

    public function test_rules_for_other_methods()
    {
        $request = new UserRequest();

        // Simulate a PUT request
        $this->app['request']->setMethod('PUT');

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertEquals('string', $rules['name']);
        $this->assertEquals('email|unique:users,email', $rules['email']);
        $this->assertEquals('string|min:6', $rules['password']);
    }

    public function test_messages()
    {
        $request = new UserRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('name.required', $messages);
        $this->assertArrayHasKey('email.required', $messages);
        $this->assertArrayHasKey('email.email', $messages);
        $this->assertArrayHasKey('email.unique', $messages);
        $this->assertArrayHasKey('password.required', $messages);
        $this->assertArrayHasKey('password.min', $messages);
        $this->assertEquals('Name is required', $messages['name.required']);
        $this->assertEquals('Email is required', $messages['email.required']);
        $this->assertEquals('Email is invalid', $messages['email.email']);
        $this->assertEquals('Email is already taken', $messages['email.unique']);
        $this->assertEquals('Password is required', $messages['password.required']);
        $this->assertEquals('Password must be at least 6 characters', $messages['password.min']);
    }

    public function test_failed_validation_for_post_method()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_failed_validation_for_put_method()
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $user = $authData['user'];

        // Test with invalid email
        $response = $this->putJson("/api/users/{$user->id}", ['email' => 'invalid-email'], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        // Test with invalid password
        $response = $this->putJson("/api/users/{$user->id}", ['password' => 'short'], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }
}
