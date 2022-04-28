<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use WithFaker;

    /**
     * Test create product
     */
    public function testCreateProduct()
    {
        // Count existed products
        $count = Product::count();
        // Product data
        $product = [
            'code' => $this->faker->unique()->randomNumber(4),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'price' => $this->faker->randomFloat(2, 0, 10000),
            'brand' => $this->faker->title(),
            'unit' => $this->faker->title(),
            'category_id' => Category::factory()->create()->id,
        ];
        // Get token
        $token = $this->authenticate();
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/products', $product);
        // Increment the count and assert the product was inserted      
        $count++;
        $this->assertEquals($count, Product::count());
        // Assert it was created     
        $response->assertStatus(201);
    }

    /**
     * Test invalid create product
     */
    public function testInvalidCreateProduct()
    {
        // Create product
        $firstProduct = Product::factory()->create();
        // Product data
        $product = [
            'code' => $firstProduct->code,
            'category_id' => 0,
        ];
        // Get token
        $token = $this->authenticate();
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/products', $product);
        // Assert it returns invalid data    
        $response->assertStatus(422);
        // Assert returned data structure cotaining errors    
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
        // Assert if catgory_id error was invalid id
        $response->assertJsonFragment([
            'category_id' => ['The selected category id is invalid.'],
        ]);
    }

    /**
     * Test update product
     */
    public function testUpdateProduct()
    {
        // Create product   
        $product = Product::factory()->create();
        // Create category
        $category = Category::factory()->create();
        // Product data
        $newProduct = [
            'code' => $this->faker->unique()->randomNumber(4),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'price' => $this->faker->randomFloat(2, 0, 10000),
            'brand' => $this->faker->title(),
            'unit' => $this->faker->title(),
            'category_id' => $category->id
        ];
        // Get token
        $token = $this->authenticate();
        // Send put request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/products/' . $product->id, $newProduct);
        // Assert it was successful
        $response->assertStatus(200);
        // Assert product was updated with the sent data
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

    /**
     * Test invalid update product
     */
    public function testInvalidUpdateProduct()
    {
        // Create tow new products
        $firstProduct = Product::factory()->create();
        $secondProduct = Product::factory()->create();
        // Product data
        $productData = [
            'code' => $firstProduct->code,
            'category_id' => 0
        ];
        // Get token
        $token = $this->authenticate();
        // Send put request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/products/' . $secondProduct->id, $productData);
        // Assert it returns invalid data
        $response->assertStatus(422);
        // Assert returned data structure containing errors 
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
        // Assert if catgory_id error was invalid id
        $response->assertJsonFragment([
            'category_id' => ['The selected category id is invalid.'],
        ]);
    }

    /**
     * Test list products
     */
    public function testListProduct()
    {
        // Get token
        $token = $this->authenticate();
        // Send get request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/products');
        // Assert it was successful
        $response->assertStatus(200);
        // Assert returned data structure
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

    /**
     * Test show product
     */
    public function testShowProduct()
    {
        // Get token
        $token = $this->authenticate();
        // Create product
        $product = Product::factory()->create();
        // Send get request with id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/products/' . $product->id);
        // Assert it was successful
        $response->assertStatus(200);
    }

    /**
     * Test invalid show product
     */
    public function testInvalidShowProduct()
    {
        // Get token
        $token = $this->authenticate();
        // Send get request with invalid id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/products/0');
        // Assert it returns not found
        $response->assertStatus(404);
    }

    /**
     * Test delete product
     */
    public function testDeleteProduct()
    {
        // Get token
        $token = $this->authenticate();
        // Create product
        $product = Product::factory()->create();
        // Count existed products
        $count = Product::count();
        // Send delete request with id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/products/' . $product->id);
        // Assert it was successful
        $response->assertStatus(200);
        // Decrement the count and assert the product was deleted
        $count--;
        $this->assertEquals($count, Product::count());
    }

    /**
     * Test invalid delete product
     */
    public function testInvalidDeleteProduct()
    {
        // Get token
        $token = $this->authenticate();
        // Send delete request with invalid id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/products/0');
        // Assert it returns not found
        $response->assertStatus(404);
    }
}
