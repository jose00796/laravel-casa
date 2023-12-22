<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_name' => $this->faker->word(),
            'cant_product' => $this->faker->numerify(),
            'mark' => $this->faker->word(),
            'price' => $this->faker->numerify(),
            'description' => $this->faker->sentence(),
            'company_name' => $this->faker->company(),
        ];
    }
}
