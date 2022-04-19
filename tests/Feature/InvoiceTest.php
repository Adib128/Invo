<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvoiceTest extends TestCase
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

    public function testListInvoice()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/invoices');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
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

    public function testShowInvoice()
    {
        $token = $this->authenticate();
        $invoice = Invoice::factory()->create();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/invoices/' . $invoice->id);
        $response->assertStatus(200);
    }

    public function testInvalidShowInvoice()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/invoices/0');
        $response->assertStatus(404);
    }
    /*
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
    */
}
