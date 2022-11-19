<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAdditionalParamsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>strtolower($this->faker->word),
            'value'=>$this->faker->numberBetween(3,7)
        ];
    }
}
