<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Artisan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create passport key
        Artisan::call('passport:install'); 

        // Create customers
        Customer::factory(10)->create();

        // Create categories
        Category::factory(10)->create();

        // Create products
        Product::factory(10)->create();
    }
}
