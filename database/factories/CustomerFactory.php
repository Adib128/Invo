<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->email(),
            'phoneNumber' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'city' => $this->faker->city(),
            'address' => $this->faker->address()
        ];
    }
}
