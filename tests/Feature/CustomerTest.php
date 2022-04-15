<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    public function authenticate()
    {
        $user = [
            'name' => 'Test',
            'email' => ($email =
                time() . mt_rand(0, 999) . 'test123@gmail.com'),
            'password' => bcrypt('12345678'),
        ];
        User::create($user);
        if (!auth()->attempt(['email' => $email, 'password' => '12345678'])) {
            return response(['message' => 'Login credentials are invaild']);
        }
        return $accessToken = auth()
            ->user()
            ->createToken('authToken')->accessToken;
    }

    public function testCreateCustomer()
    {
        $count = Customer::count();
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/customers', [
            'phoneNumber' => mt_rand(10000000, 99999999),
            'name' => 'Aouadi Adib',
            'email' => time() . '@gmail.com',
            'city' => 'Jendouba',
            'address' => 'rue 01 bullaregia',
        ]);
        $count++;
        $this->assertEquals($count, Customer::count());
        $response->assertStatus(201);
    }

    public function testInvalidCreateCustomer()
    {
        $customer = [
            'phoneNumber' => random_int(10000000, 99999998),
            'name' => 'Aouadi Adib',
            'email' => time() . 'test@gmail.com',
            'city' => 'Jendouba',
            'address' => 'rue 01 bullaregia',
        ];
        Customer::create($customer);
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/customers', [$customer]);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'errors' => ['email', 'phoneNumber'],
        ]);
    }

    public function updateCustomer(){
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/customers', [
            'phoneNumber' => mt_rand(10000000, 99999999),
            'name' => 'Aouadi Adib',
            'email' => time() . '@gmail.com',
            'city' => 'Jendouba',
            'address' => 'rue 01 bullaregia',
        ]);
        $response->assertStatus(200);
    }

    public function testListCustomer()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/customers');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'phoneNumber',
                    'city',
                    'address'
                ]
            ]
        ]);
    }

    public function testShowCustomer()
    {
        $token = $this->authenticate();
        $customer = Customer::select('*')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/customers/' . $customer->id);
        $response->assertStatus(200);
    }

    public function testInvalidShowCustomer()
    {
        $token = $this->authenticate();
        $customer = Customer::select('*')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/customers/0');
        $response->assertStatus(404);
    }

    public function testDeleteCustomer()
    {
        $token = $this->authenticate();
        $customer = Customer::factory()->create();
        $count = Customer::count();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/customers/' . $customer->id);
        $response->assertStatus(200);
        $count--;
        $this->assertEquals($count, Customer::count());
    }

    public function testInvalidDeleteCustomer()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/customers/0');
        $response->assertStatus(404);
    }

}
