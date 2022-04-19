<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use WithFaker;

    public function authenticate()
    {
        $user = User::factory()->create();
        auth()->attempt([
            'email' => $user->email,
            'password' => 'password',
        ]);
        return $accessToken = auth()
            ->user()
            ->createToken('authToken')->accessToken;
    }

    public function testCreateCustomer()
    {
        $count = Customer::count();
        $customer = [
            'phoneNumber' => $this->faker
                ->unique()
                ->numberBetween(10000000, 99999999),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'city' => $this->faker->city,
            'address' => $this->faker->address,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/customers', $customer);
        $count++;
        $this->assertEquals($count, Customer::count());
        $response->assertStatus(201);
    }

    public function testInvalidCreateCustomer()
    {
        $firstCustomer = Customer::factory()->create();
        $customer = [
            'phoneNumber' => $firstCustomer->phoneNumber,
            'email' => $firstCustomer->email,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/customers', $customer);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'errors' => ['name', 'email', 'phoneNumber', 'address'],
        ]);
    }

    public function testUpdateCustomer()
    {
        $customer = Customer::factory()->create();
        $newCustomer = [
            'phoneNumber' => $this->faker
                ->unique()
                ->numberBetween(10000000, 99999999),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'city' => $this->faker->city,
            'address' => $this->faker->address,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/customers/' . $customer->id, $newCustomer);
        $response->assertStatus(200);
        $response->assertExactJson([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data' => [
                'id' => $customer->id,
                'name' => $newCustomer['name'],
                'email' => $newCustomer['email'],
                'phoneNumber' => $newCustomer['phoneNumber'],
                'city' => $newCustomer['city'],
                'address' => $newCustomer['address'],
            ],
        ]);
    }

    public function testInvalidUpdateCustomer()
    {
        $firstCustomer = Customer::factory()->create();
        $secondCustomer = Customer::factory()->create();
        $customerData = [
            'email' => $firstCustomer->email,
            'phoneNumber' => $firstCustomer->phoneNumber,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/customers/' . $secondCustomer->id, $customerData);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'errors' => ['name', 'email', 'phoneNumber', 'address'],
        ]);
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
                    'address',
                ],
            ],
        ]);
    }

    public function testShowCustomer()
    {
        $token = $this->authenticate();
        $customer = Customer::factory()->create();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/customers/' . $customer->id);
        $response->assertStatus(200);
    }

    public function testInvalidShowCustomer()
    {
        $token = $this->authenticate();
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
