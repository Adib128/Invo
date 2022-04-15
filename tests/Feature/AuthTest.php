<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function testRegister()
    {
        $response = $this->json('POST', '/register', [
            'name' => 'Test',
            'email' => time() . 'test@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => ['user', 'access_token'],
        ]);
    }

    public function testInvalidRegister(){
        $user = User::select('*')->first();
        $response = $this->json('POST', '/register', [
            'name' => 'Test',
            'email' => $user->email,
            'password' => bcrypt('12345678'),
        ]);
        $response->assertStatus(422);
    }

    public function testLogin(){
        $password = '12345678';
        User::create([
            'name' => $name = 'Test',
            'email' => $email = time() . '@gmail.com',
            'password' => bcrypt($password),
        ]);
        $response = $this->json('POST', '/login', [
            'email' => $email,
            'password' => $password
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => ['user', 'access_token']
        ]);
    }

    public function testInvalidLogin(){
        $response = $this->json('POST', '/login', [
            'email' => 'aaa@gmail.com',
            'password' => '12345678'
        ]);
        $response->assertStatus(401);
    }
}
