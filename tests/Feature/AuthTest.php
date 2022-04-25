<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker;

    /**
     * Test user registration
     */
    public function testRegister()
    {
        // User data
        $userData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
        ];
        // Send post request
        $response = $this->json('POST', '/api/register', $userData);
        // Assert it was successful
        $response->assertStatus(200);
        // Assert returned data structure cotaining access_token
        $response->assertJsonStructure([
            'success',
            'data' => ['user', 'access_token'],
        ]);
    }

    /**
     * Test invalid user registration
     */
    public function testInvalidRegister()
    {
        // Create user
        $user = User::factory()->create();

        // Send post request
        $response = $this->json('POST', '/api/register', [
            'email' => $user->email, // Created user email
        ]);
        // Assert it returns invalid data
        $response->assertStatus(422);
        // Assert returned data structure cotaining errors
        $response->assertJsonStructure([
            'success',
            'message',
            'errors' => ['name', 'email', 'password'],
        ]);
    }

    /**
     * Test user login
     */
    public function testLogin()
    {
        // Create user
        $user = User::factory()->create();

        // User data
        $userData = [
            'email' => $user->email,
            'password' => 'password',
        ];
        // Send post request
        $response = $this->json('POST', '/api/login', $userData);
        // Assert it was successful
        $response->assertStatus(200);
        // Assert returned data structure cotaining access_token
        $response->assertJsonStructure([
            'success',
            'data' => ['user', 'access_token'],
        ]);
    }

    /**
     * Test invalid user login
     */
    public function testInvalidLogin()
    {
        // User data
        $userData = [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ];
        // Send post request
        $response = $this->json('POST', '/api/login', $userData);
        // Assert it was invalid credentials
        $response->assertStatus(401);
    }

    /**
     * Test user change password
     */
    public function testChangePassword()
    {
        // Create user
        $user = User::factory()->create();
        // Attempt login
        auth()->attempt([
            'email' => $user->email,
            'password' => 'password',
        ]);
        // Generate token
        $token = auth()
            ->user()
            ->createToken('authToken')->accessToken;
        // User passwords    
        $payload = [
            'current_password' => 'password',
            'new_password' => '12345678',
            'new_confirm_password' => '12345678',
        ];
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/change-password', $payload);
        // Assert it was successful
        $response->assertStatus(200);
        // Assert returned exact json response   
        $response->assertExactJson([
            'success' => true,
            'message' => 'Password change successfully',
        ]);
    }

    /**
     * Test invalid user change password
     */
    public function testInvalidChangePassword()
    {
        // Get token
        $token = $this->authenticate();
        // User passwords 
        $payload = [
            'current_password' => 'password123',
            'new_password' => 'a115f5f5s',
            'new_confirm_password' => '12a598g88g',
        ];
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/change-password', $payload);
        // Assert it returns invalid data    
        $response->assertStatus(422);
        // Assert returned data structure containing errors    
        $response->assertJsonStructure([
            'success',
            'errors' => ['current_password', 'new_confirm_password'],
        ]);
    }

    /**
     * Test user logout
     */
    public function testLogout()
    {
        // Get token
        $token = $this->authenticate();
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/logout');
        // Assert it was successful
        $response->assertStatus(200);
        // Assert returned data structure
        $response->assertExactJson([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Test invalid user logout
     */
    public function testInvalidLogout()
    {
        // Generate fake token
        $token = $this->faker->text();
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/logout');
        // Assert it was Unauthorized
        $response->assertStatus(401);
    }

    /**
     * Test invalid access
     */
    public function testInvalidAccess()
    {
        // Send post request without token
        $response = $this->json('GET', '/api/products');
        // Assert it was Unauthorized
        $response->assertStatus(401);

        // Send post request without token
        $response = $this->json('GET', '/api/invoices');
        // Assert it was Unauthorized
        $response->assertStatus(401);

        // Send post request without token
        $response = $this->json('GET', '/api/customers');
        // Assert it was Unauthorized
        $response->assertStatus(401);
    }
}
