<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
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

    public function testCreateProduct()
    {
        $count = Product::count();
        $product = [
            'code' => $this->faker->unique()->randomNumber(4),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'price' => 10.2,
            'brand' => $this->faker->title(),
            'unit' => $this->faker->title(),
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/products', $product);
        $count++;
        $this->assertEquals($count, Product::count());
        $response->assertStatus(201);
    }

    public function testInvalidCreateProduct()
    {
        $firstProduct = Product::factory()->create();
        $product = [
            'code' => $firstProduct->code,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/products', $product);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'errors' => ['code', 'name', 'price', 'brand', 'unit'],
        ]);
    }

    public function testUpdateProduct()
    {
        $product = Product::factory()->create();
        $newProduct = [
            'code' => $this->faker->unique()->randomNumber(4),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'price' => 10.2,
            'brand' => $this->faker->title(),
            'unit' => $this->faker->title(),
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/products/' . $product->id, $newProduct);
        $response->assertStatus(200);
        $response->assertExactJson([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => [
                'id' => $product->id,
                'code' => $newProduct['code'],
                'name' => $newProduct['name'],
                'description' => $newProduct['description'],
                'price' => $newProduct['price'],
                'brand' => $newProduct['brand'],
                'unit' => $newProduct['unit'],
            ],
        ]);
    }

    public function testInvalidUpdateProduct()
    {
        $firstProduct = Product::factory()->create();
        $secondProduct = Product::factory()->create();
        $productData = [
            'code' => $firstProduct->code
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/products/' . $secondProduct->id, $productData);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'errors' => ['code', 'name', 'price', 'brand', 'unit'],
        ]);
    }

    public function testListProduct()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/products');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
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
        ]);
    }

    public function testShowProduct()
    {
        $token = $this->authenticate();
        $product = Product::factory()->create();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/products/' . $product->id);
        $response->assertStatus(200);
    }

    public function testInvalidShowProduct()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/products/0');
        $response->assertStatus(404);
    }

    public function testDeleteProduct()
    {
        $token = $this->authenticate();
        $product = Product::factory()->create();
        $count = Product::count();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/products/' . $product->id);
        $count--;
        $response->assertStatus(200);
        $this->assertEquals($count, Product::count());
    }

    public function testInvalidDeleteProduct()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/products/0');
        $response->assertStatus(404);
    }
}
