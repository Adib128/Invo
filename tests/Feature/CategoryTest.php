<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use WithFaker;

    public function testCreateCategory()
    {
        $count = Category::count();
        $category = [
            'code' => $this->faker->unique()->randomNumber(4),
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug()
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/categories', $category);

        $count++;
        $this->assertEquals($count, Category::count());

        $response->assertStatus(201);

        $this->assertDatabaseHas('categories', [
            'code' => $category['code'],
        ]);

        $category = Category::first();
        $this->get("/api/categories/$category->id")->assertSee(
            $category->code
        );
    }

    public function testInvalidCreateCategory()
    {
        $firstCategory = Category::factory()->create();
        $count = Category::count();
        $category = [
            'code' => $firstCategory->code,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/categories', $category);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'success',
            'errors' => [
                'code',
                'name',
                'slug'
            ],
        ]);

        $this->assertEquals($count, Category::count());
    }

    public function testUpdateCategory()
    {
        $category = Category::factory()->create();
        $newCategory = [
            'code' => $this->faker->unique()->randomNumber(4),
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug()
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/categories/' . $category->id, $newCategory);
        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'code' => $newCategory['code'],
            'name' => $newCategory['name'],
            'slug' => $newCategory['slug'],
        ]);
    }

    public function testInvalidUpdateCategory()
    {
        $firstCategory = Category::factory()->create();
        $secondCategory = Category::factory()->create();
        $categoryData = [
            'code' => $secondCategory->code,
        ];
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/categories/' . $firstCategory->id, $categoryData);

        $response->assertStatus(422);
        
        $response->assertJsonStructure([
            'success',
            'errors' => [
                'code',
                'name',
                'slug'
            ],
        ]);
    }

    public function testListCategory()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/categories');
        $response->assertStatus(200);
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

    public function testShowCategory()
    {
        $token = $this->authenticate();
        $category = Category::factory()->create();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/categories/' . $category->id);
        $response->assertStatus(200);
    }

    public function testInvalidShowCustomer()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/categories/0');
        $response->assertStatus(404);
    }

    public function testDeleteCustomer()
    {
        $token = $this->authenticate();
        $category = Category::factory()->create();
        $count = Category::count();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/categories/' . $category->id);
        $response->assertStatus(200);
        $count--;
        $this->assertEquals($count, Category::count());
    }

    public function testInvalidDeleteCategory()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/categories/0');
        $response->assertStatus(404);
    }
}
