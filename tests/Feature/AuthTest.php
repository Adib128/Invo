<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker;

    public function testRegister()
    {
        $user = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
        ];
        $response = $this->json('POST', '/api/register', $user);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => ['user', 'access_token'],
        ]);
    }

    public function testInvalidRegister()
    {
        $user = User::factory()->create();
        $response = $this->json('POST', '/api/register', [
            'email' => $user->email,
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'errors' => ['name', 'email', 'password'],
        ]);
    }

    public function testLogin()
    {
        $user = User::factory()->create();
        $userData = [
            'email' => $user->email,
            'password' => 'password',
        ];
        $response = $this->json('POST', '/api/login', $userData);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => ['user', 'access_token'],
        ]);
    }

    public function testInvalidLogin()
    {
        $userData = [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ];
        $response = $this->json('POST', '/api/login', $userData);
        $response->assertStatus(401);
    }

    public function testChangePassword()
    {
        $user = User::factory()->create();
        auth()->attempt([
            'email' => $user->email,
            'password' => 'password',
        ]);
        $token = auth()
            ->user()
            ->createToken('authToken')->accessToken;

        $payload = [
            'current_password' => 'password',
            'new_password' => '12345678',
            'new_confirm_password' => '12345678',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/change-password', $payload);

        $response->assertStatus(200);

        $response->assertExactJson([
            'success' => true,
            'message' => 'Password change successfully',
        ]);
    }

    public function testInvalidChangePassword()
    {
        $token = $this->authenticate();
        $payload = [
            'current_password' => 'password123',
            'new_password' => 'a115f5f5s',
            'new_confirm_password' => '12a598g88g',
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/change-password', $payload);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'success',
            'errors' => ['current_password', 'new_confirm_password'],
        ]);
    }

    public function testLogout()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/logout');

        $response->assertStatus(200);

        $response->assertExactJson([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);
    }

    public function testInvalidLogout()
    {
        $token  = $this->faker->text();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/logout');

        $response->assertStatus(401);
    }

    public function testInvalidAccess()
    {
        $response = $this->json('GET', '/api/products');
        $response->assertStatus(401);

        $response = $this->json('GET', '/api/invoices');
        $response->assertStatus(401);

        $response = $this->json('GET', '/api/customers');
        $response->assertStatus(401);
    }
}
