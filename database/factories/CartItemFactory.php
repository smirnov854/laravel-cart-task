<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id'=>$product=Product::all()->random(),
            'cart_id'=>Cart::all()->random(),
            'quantity'=>$val=$this->faker->numberBetween(3,5),
            'price'=>$product->price,
            'total'=>$val*$product->price
        ];
    }
}
