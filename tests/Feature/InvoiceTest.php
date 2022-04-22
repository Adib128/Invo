<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use WithFaker;

    public function testListInvoice()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/invoices');
        $response->assertStatus(200);
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

    public function testCreateInvoice()
    {
        $this->withoutExceptionHandling();
        $count = Invoice::count();
        $invoice = [
            'reference' => $this->faker->unique()->randomNumber(),
            'dueDate' => $this->faker->date(),
            'subTotal' => $this->faker->randomFloat(2, 0, 10000),
            'tax' => $this->faker->randomFloat(2, 0, 10000),
            'discount' => $this->faker->randomNumber(),
            'total' => $this->faker->randomFloat(2, 0, 10000),
            'customer_id' => Customer::factory()->create()->id,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/invoices', $invoice);
        $response->assertStatus(201);

        $count++;
        $this->assertEquals($count, Invoice::count());

        $this->assertDatabaseHas('invoices', [
            'reference' => $invoice['reference'],
        ]);

        $invoice = Invoice::first();
        $this->get("/api/invoices/$invoice->id")->assertSee(
            $invoice->reference
        );
    }

    public function testInvalidCreateInvoice()
    {
        $firstInvoice = Invoice::factory()->create();
        $invoice = [
            'reference' => $firstInvoice->reference,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/invoices', $invoice);
        $response->assertStatus(422);
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
        $this->assertDatabaseHas('invoices', [
            'reference' => $invoice['reference'],
        ]);
    }

    public function testUpdateInvoice()
    {
        $invoice = Invoice::factory()->create();
        $newInvoice = [
            'reference' => 33 + $this->faker->unique()->randomNumber(),
            'dueDate' => $this->faker->date(),
            'subTotal' => $this->faker->randomFloat(2, 0, 10000),
            'tax' => $this->faker->randomFloat(2, 0, 10000),
            'discount' => $this->faker->randomNumber(),
            'total' => $this->faker->randomFloat(2, 0, 10000),
            'customer_id' => Customer::factory()->create()->id,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/invoices/' . $invoice->id, $newInvoice);
        $response->assertStatus(200);
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

    public function testInvalidUpdateInvoice()
    {
        $firstInvoice = Invoice::factory()->create();
        $secondInvoice = Invoice::factory()->create();
        $invoiceData = [
            'reference' => $secondInvoice->reference,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/invoices/' . $firstInvoice->id, $invoiceData);
        $response->assertStatus(422);
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

    public function testShowInvoice()
    {
        $token = $this->authenticate();
        $invoice = Invoice::factory()->create();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/invoices/' . $invoice->id);
        $response->assertStatus(200);
    }

    public function testInvalidShowInvoice()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/invoices/0');
        $response->assertStatus(404);
    }

    public function testDeleteInvoice()
    {
        $token = $this->authenticate();
        $invoice = Invoice::factory()->create();
        $count = Invoice::count();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/invoices/' . $invoice->id);
        $response->assertStatus(200);

        $count--;
        $this->assertEquals($count, Invoice::count());

        $this->assertDatabaseMissing('invoices', [
            'deleted_at' => null,
            'reference' => $invoice->reference,
        ]);
    }

    public function testInvalidDeleteInvoice()
    {
        $token = $this->authenticate();
        $count = Invoice::count();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/invoices/0');

        $response->assertStatus(404);

        $this->assertEquals($count, Invoice::count());
    }
}
