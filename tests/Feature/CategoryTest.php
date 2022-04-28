<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use WithFaker;

    /**
     * Test create category
     */
    public function testCreateCategory()
    {
        // Count existed categories
        $count = Category::count();
        // Category data
        $category = [
            'code' => $this->faker->unique()->randomNumber(4),
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug()
        ];
        // Get token
        $token = $this->authenticate();
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/categories', $category);
        // Increment the count and assert the category was inserted    
        $count++;
        $this->assertEquals($count, Category::count());
        // Assert it was created 
        $response->assertStatus(201);
    }

    /**
     * Test invalid create category
     */
    public function testInvalidCreateCategory()
    {
        // Create category
        $firstCategory = Category::factory()->create();
        // Count existed category
        $count = Category::count();
        // Category data
        $category = [
            'code' => $firstCategory->code,
        ];
        // Get token
        $token = $this->authenticate();
        // Send post request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/categories', $category);
        // Assert it returns invalid data    
        $response->assertStatus(422);
        // Assert returned data structure cotaining errors    
        $response->assertJsonStructure([
            'success',
            'errors' => [
                'code',
                'name',
                'slug'
            ],
        ]);
        // Assert the category was not inserted 
        $this->assertEquals($count, Category::count());
    }

    /**
     * Test update category
     */
    public function testUpdateCategory()
    {
        // Create category
        $category = Category::factory()->create();
        // Category data
        $newCategory = [
            'code' => $this->faker->unique()->randomNumber(4),
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug()
        ];
        // Get token
        $token = $this->authenticate();
        // Send put request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/categories/' . $category->id, $newCategory);
        // Assert it was successful
        $response->assertStatus(200);
        // Assert category was updated with the sent data
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'code' => $newCategory['code'],
            'name' => $newCategory['name'],
            'slug' => $newCategory['slug'],
        ]);
    }

    /**
     * Test invalid update category
     */
    public function testInvalidUpdateCategory()
    {
        // Create tow new categories
        $firstCategory = Category::factory()->create();
        $secondCategory = Category::factory()->create();
        // Category data
        $categoryData = [
            'code' => $secondCategory->code,
        ];
        // Get token
        $token = $this->authenticate();
        // Send put request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/categories/' . $firstCategory->id, $categoryData);
        // Assert it returns invalid data  
        $response->assertStatus(422);
        // Assert returned data structure containing errors 
        $response->assertJsonStructure([
            'success',
            'errors' => [
                'code',
                'name',
                'slug'
            ],
        ]);
    }
    /**
     * Test list categories
     */
    public function testListCategory()
    {
        // Get token
        $token = $this->authenticate();
        // Send get request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/categories');
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
                    'slug',
                    'products'
                ],
            ],
        ]);
    }

    /**
     * Test show category
     */
    public function testShowCategory()
    {
        // Get token
        $token = $this->authenticate();
        // Create category
        $category = Category::factory()->create();
        // Send get request with id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/categories/' . $category->id);
        // Assert it was successful
        $response->assertStatus(200);
    }

    /**
     * Test invalid show category
     */
    public function testInvalidShowCategory()
    {
        // Get token
        $token = $this->authenticate();
        // Send get request with invalid id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/categories/0');
        // Assert it returns not found
        $response->assertStatus(404);
    }

    /**
     * Test delete category
     */
    public function testDeleteCategory()
    {
        // Get token
        $token = $this->authenticate();
        // Create category
        $category = Category::factory()->create();
        // Count existed categories
        $count = Category::count();
        // Send delete request with id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/categories/' . $category->id);
        // Assert it was successful
        $response->assertStatus(200);
        // Decrement the count and assert the category was deleted
        $count--;
        $this->assertEquals($count, Category::count());
    }

    /**
     * Test invalid delete category
     */
    public function testInvalidDeleteCategory()
    {
        // Get token
        $token = $this->authenticate();
        // Send delete request with invalid id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/categories/0');
        // Assert it returns not found
        $response->assertStatus(404);
    }
}
