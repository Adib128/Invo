<?php

namespace Tests\Feature;

use App\Models\Category;
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
            'price' => $this->faker->randomFloat(2, 0, 10000),
            'brand' => $this->faker->title(),
            'unit' => $this->faker->title(),
            'category_id' => Category::factory()->create()->id,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/products', $product);
        $count++;
        $this->assertEquals($count, Product::count());
        $response->assertStatus(201);
    }

    public function testInvalidCreateProduct()
    {
        $firstProduct = Product::factory()->create();
        $product = [
            'code' => $firstProduct->code,
            'category_id' => 0,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/products', $product);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'errors' => [
                'code',
                'name',
                'price', 
                'brand',
                'unit',
                'category_id'
            ]
        ]);
        $response->assertJsonFragment([
            'category_id' => ['The selected category id is invalid.'],
        ]);
    }

    public function testUpdateProduct()
    {
        $product = Product::factory()->create();
        $category = Category::factory()->create();
        $newProduct = [
            'code' => $this->faker->unique()->randomNumber(4),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'price' => $this->faker->randomFloat(2, 0, 10000),
            'brand' => $this->faker->title(),
            'unit' => $this->faker->title(),
            'category_id' => $category->id
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/products/' . $product->id, $newProduct);
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
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name
                ],
            ],
        ]);
    }

    public function testInvalidUpdateProduct()
    {
        $firstProduct = Product::factory()->create();
        $secondProduct = Product::factory()->create();
        $productData = [
            'code' => $firstProduct->code,
            'category_id' => 0
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/products/' . $secondProduct->id, $productData);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'errors' => [
                'code',
                'name',
                'price', 
                'brand',
                'unit',
                'category_id'
            ]
        ]);
        $response->assertJsonFragment([
            'category_id' => ['The selected category id is invalid.'],
        ]);
    }

    public function testListProduct()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/products');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'current_page',
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
        ])->json('GET', '/api/products/' . $product->id);
        $response->assertStatus(200);
    }

    public function testInvalidShowProduct()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/products/0');
        $response->assertStatus(404);
    }

    public function testDeleteProduct()
    {
        $token = $this->authenticate();
        $product = Product::factory()->create();
        $count = Product::count();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/products/' . $product->id);
        $count--;
        $response->assertStatus(200);
        $this->assertEquals($count, Product::count());
    }

    public function testInvalidDeleteProduct()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/products/0');
        $response->assertStatus(404);
    }
}
