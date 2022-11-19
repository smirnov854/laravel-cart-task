<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiCartTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use WithFaker;
    public function test_cart_create()
    {
        $response = $this->post('/api/carts');
        $response->assertStatus(201)->assertJsonStructure([
            'message',
            'cart_uuid'
        ]);
    }

    public function test_cart_item_add_error()
    {

        $response = $this->post('/api/add-to-cart',[
            ''
        ]);
        $response->assertStatus(422);
    }

    public function test_cart_item_add_prod_not_exists()
    {
        $cart = Cart::create([
            'uuid'=>$this->faker->unique()->uuid(),
        ]);
        $response = $this->post('/api/add-to-cart',[
            'uuid'=>$cart->uuid,
            'quantity'=>1,
            'product_id'=>9999999999
        ]);
        $response->assertStatus(404)->assertJsonStructure([
            'message',
        ]);
    }

    public function test_cart_item_add_cart_error()
    {
        $product = Product::create([
            'name'=>'test-test-test',
            'description'=>$this->faker->paragraph,
            'price'=> mt_rand(99, 4999) / 100
        ]);
        $response = $this->post('/api/add-to-cart',[
            'uuid'=>'123',
            'quantity'=>1,
            'product_id'=>$product->id
        ]);
        $response->assertStatus(404)->assertJsonStructure([
            'message',
        ]);
    }


    public function test_cart_item_add_prod()
    {
        $cart = Cart::create([
            'uuid'=>$this->faker->unique()->uuid(),
        ]);
        $product = Product::factory()->create();
        $response = $this->post('/api/add-to-cart',[
            'uuid'=>$cart->uuid,
            'quantity'=>1,
            'product_id'=>$product->id
        ]);
        $response->assertStatus(201)->assertJsonStructure([
            'message',
        ]);
    }




}
