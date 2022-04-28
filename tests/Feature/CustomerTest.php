<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use WithFaker;

    /**
     * Test create customer
     */
    public function testCreateCustomer()
    {
        // Count existed customers
        $count = Customer::count();
        // Customer data
        $customer = [
            'phoneNumber' => $this->faker
                ->unique()
                ->numberBetween(10000000, 99999999),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'city' => $this->faker->city,
            'address' => $this->faker->address,
        ];
        // Get token
        $token = $this->authenticate();
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/customers', $customer);
        // Increment the count and assert the customer was inserted    
        $count++;
        $this->assertEquals($count, Customer::count());
        // Assert it was created 
        $response->assertStatus(201);
    }

    /**
     * Test invalid create customer
     */
    public function testInvalidCreateCustomer()
    {
        // Create customer
        $firstCustomer = Customer::factory()->create();
        // Customer data
        $customer = [
            'phoneNumber' => $firstCustomer->phoneNumber,
            'email' => $firstCustomer->email,
        ];
        // Get token
        $token = $this->authenticate();
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/customers', $customer);
        // Assert it returns invalid data    
        $response->assertStatus(422);
        // Assert returned data structure cotaining errors 
        $response->assertJsonStructure([
            'success',
            'errors' => ['name', 'email', 'phoneNumber', 'address'],
        ]);
    }

    /**
     * Test update customer
     */
    public function testUpdateCustomer()
    {
        // Create customer
        $customer = Customer::factory()->create();
        // Customer data 
        $newCustomer = [
            'phoneNumber' => $this->faker
                ->unique()
                ->numberBetween(10000000, 99999999),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'city' => $this->faker->city,
            'address' => $this->faker->address,
        ];
        // Get token
        $token = $this->authenticate();
        // Send put request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/customers/' . $customer->id, $newCustomer);
        // Assert it was successful
        $response->assertStatus(200);
        // Assert the returnd response with updated data
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

    /**
     * Test invalid update customer
     */
    public function testInvalidUpdateCustomer()
    {
        // Create tow new customers
        $firstCustomer = Customer::factory()->create();
        $secondCustomer = Customer::factory()->create();
        // Customer data
        $customerData = [
            'email' => $firstCustomer->email,
            'phoneNumber' => $firstCustomer->phoneNumber,
        ];
        // Get token
        $token = $this->authenticate();
        // Send put request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/customers/' . $secondCustomer->id, $customerData);
        // Assert it returns invalid data  
        $response->assertStatus(422);
        // Assert returned data structure containing errors 
        $response->assertJsonStructure([
            'success',
            'errors' => ['name', 'email', 'phoneNumber', 'address'],
        ]);
    }

    /**
     * Test list customers
     */
    public function testListCustomer()
    {
        // Get token
        $token = $this->authenticate();
        // Send get request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/customers');
        // Assert it was successful
        $response->assertStatus(200);
        // Assert returned data structure
        $response->assertJsonStructure([
            'success',
            'current_page',
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

    /**
     * Test show customer
     */
    public function testShowCustomer()
    {
        // Get token
        $token = $this->authenticate();
        // Create category
        $customer = Customer::factory()->create();
        // Send get request with id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/customers/' . $customer->id);
        // Assert it was successful
        $response->assertStatus(200);
    }

    /**
     * Test invalid show customer
     */
    public function testInvalidShowCustomer()
    {
        // Get token
        $token = $this->authenticate();
        // Send get request with invalid id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/customers/0');
        // Assert it returns not found
        $response->assertStatus(404);
    }

    /**
     * Test delete customer
     */
    public function testDeleteCustomer()
    {
        // Get token
        $token = $this->authenticate();
        // Create customer
        $customer = Customer::factory()->create();
        // Count existed customers
        $count = Customer::count();
        // Send delete request with id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/customers/' . $customer->id);
        // Assert it was successful
        $response->assertStatus(200);
        // Decrement the count and assert the customer was deleted
        $count--;
        $this->assertEquals($count, Customer::count());
    }

    /**
     * Test invalid delete customer
     */
    public function testInvalidDeleteCustomer()
    {
        // Get token
        $token = $this->authenticate();
        // Send delete request with invalid id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/customers/0');
        // Assert it returns not found
        $response->assertStatus(404);
    }
}
