<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'reference' => $this->faker->randomNumber(),
            'dueDate' => $this->faker->date(),
            'subTotal' => $this->faker->randomFloat(),
            'tax' => $this->faker->randomFloat(),
            'discount' => $this->faker->randomNumber(),
            'total' => $this->faker->randomFloat(),
            'customer_id' => Customer::factory(),
        ];
    }
}
