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
        $response = $this->json('POST', '/register', $user);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => ['user', 'access_token'],
        ]);
    }

    public function testInvalidRegister()
    {
        $user = User::factory()->create();
        $response = $this->json('POST', '/register', [
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
            'password' => 'password'
        ];
        $response = $this->json('POST', '/login', $userData);
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
        $response = $this->json('POST', '/login', $userData);
        $response->assertStatus(401);
    }
}
