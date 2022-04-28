<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use WithFaker;

    /*
     * Test create invoice
     */
    public function testCreateInvoice()
    {
        // Count existed invoices
        $count = Invoice::count();
        // Invoice data
        $invoice = [
            'reference' => $this->faker->unique()->randomNumber(),
            'dueDate' => $this->faker->date(),
            'subTotal' => $this->faker->randomFloat(2, 0, 10000),
            'tax' => $this->faker->randomFloat(2, 0, 10000),
            'discount' => $this->faker->randomNumber(),
            'total' => $this->faker->randomFloat(2, 0, 10000),
            'customer_id' => Customer::factory()->create()->id, // Customer id
        ];
        // Get token
        $token = $this->authenticate();
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/invoices', $invoice);
        $response->assertStatus(201);
        // Increment the count and assert the customer was inserted    
        $count++;
        $this->assertEquals($count, Invoice::count());
        // Asser it is inserted on database    
        $this->assertDatabaseHas('invoices', [
            'reference' => $invoice['reference'],
        ]);
        // Checking if it is inserted with id
        $invoice = Invoice::first();
        $this->get("/api/invoices/$invoice->id")->assertSee(
            $invoice->reference
        );
    }

    /**
     * Test invalid create invoice
     */
    public function testInvalidCreateInvoice()
    {
        // Create invoice
        $firstInvoice = Invoice::factory()->create();
        // Invoice data
        $invoice = [
            'reference' => $firstInvoice->reference,
        ];
        // Get token
        $token = $this->authenticate();
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/invoices', $invoice);
        // Assert it returns invalid data
        $response->assertStatus(422);
        // Assert returned data structure cotaining errors
        $response->assertJsonStructure([
            'success',
            'errors' => [
                'reference',
                'dueDate',
                'subTotal',
                'tax',
                'discount',
                'total',
                'customer_id',
            ],
        ]);
        // Assert invoices doesn't added
        $this->assertDatabaseHas('invoices', [
            'reference' => $invoice['reference'],
        ]);
    }

    /**
     * Test update invoice
     */
    public function testUpdateInvoice()
    {
        // Create invoice
        $invoice = Invoice::factory()->create();
        // Invoice data 
        $newInvoice = [
            'reference' => 33 + $this->faker->unique()->randomNumber(),
            'dueDate' => $this->faker->date(),
            'subTotal' => $this->faker->randomFloat(2, 0, 10000),
            'tax' => $this->faker->randomFloat(2, 0, 10000),
            'discount' => $this->faker->randomNumber(),
            'total' => $this->faker->randomFloat(2, 0, 10000),
            'customer_id' => Customer::factory()->create()->id,
        ];
        // Get token
        $token = $this->authenticate();
        // Send put request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/invoices/' . $invoice->id, $newInvoice);
        // Assert it was successful
        $response->assertStatus(200);
        // Assert the returnd response with updated data
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'reference' => $newInvoice['reference'],
            'dueDate' => $newInvoice['dueDate'],
            'subTotal' => $newInvoice['subTotal'],
            'tax' => $newInvoice['tax'],
            'discount' => $newInvoice['discount'],
            'total' => $newInvoice['total'],
            'customer_id' => $newInvoice['customer_id'],
        ]);
    }

    /**
     * Test invalid update invoice
     */
    public function testInvalidUpdateInvoice()
    {
        // Create tow new invoices
        $firstInvoice = Invoice::factory()->create();
        $secondInvoice = Invoice::factory()->create();
        // Invoice data
        $invoiceData = [
            'reference' => $secondInvoice->reference,
        ];
        // Get token
        $token = $this->authenticate();
        // Send put request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/invoices/' . $firstInvoice->id, $invoiceData);
        // Assert it returns invalid data  
        $response->assertStatus(422);
        // Assert returned data structure containing errors
        $response->assertJsonStructure([
            'success',
            'errors' => [
                'reference',
                'dueDate',
                'subTotal',
                'tax',
                'discount',
                'total',
                'customer_id',
            ],
        ]);
    }

    /**
     * Test list invoices
     */
    public function testListInvoice()
    {
        // Get token
        $token = $this->authenticate();
        // Send get request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/invoices');
        // Assert it was successful
        $response->assertStatus(200);
        // Assert returned data structure
        $response->assertJsonStructure([
            'success',
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'reference',
                    'dueDate',
                    'subTotal',
                    'tax',
                    'discount',
                    'total',
                    'products' => [
                        '*' => [
                            'id',
                            'code',
                            'name',
                            'description',
                            'price',
                            'brand',
                            'unit',
                        ],
                    ],
                    'customer' => [
                        'id',
                        'name',
                        'email',
                        'phoneNumber',
                        'city',
                        'address',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test show invoice
     */
    public function testShowInvoice()
    {
        // Get token
        $token = $this->authenticate();
        // Create invoice
        $invoice = Invoice::factory()->create();
        // Send get request with id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/invoices/' . $invoice->id);
        // Assert it was successful
        $response->assertStatus(200);
    }

    /**
     * Test invalid show invoice
     */
    public function testInvalidShowInvoice()
    {
        // Get token
        $token = $this->authenticate();
        // Send get request with invalid id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/invoices/0');
        // Assert it returns not found
        $response->assertStatus(404);
    }

    /**
     * Test delete invoice
     */
    public function testDeleteInvoice()
    {
        // Get token
        $token = $this->authenticate();
        // Create invoice
        $invoice = Invoice::factory()->create();
        // Count existed invoices
        $count = Invoice::count();
        // Send delete request with id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/invoices/' . $invoice->id);
        // Assert it was successful
        $response->assertStatus(200);
        // Decrement the count and assert the customer was deleted    
        $count--;
        $this->assertEquals($count, Invoice::count());

        // Assert create invoice is deleted in database
        $this->assertDatabaseMissing('invoices', [
            'deleted_at' => null,
            'reference' => $invoice->reference,
        ]);
    }

    /**
     * Test invalid delete invoice
     */
    public function testInvalidDeleteInvoice()
    {
        // Get token
        $token = $this->authenticate();
        // Count existed invoices
        $count = Invoice::count();
        // Send delete request with invalid id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/invoices/0');
        // Assert it returns not found    
        $response->assertStatus(404);
        // Assert no invoice deleted from database  
        $this->assertEquals($count, Invoice::count());
    }
}
