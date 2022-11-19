<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiCartItemTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use WithFaker;

    public function test_cart_item_change_value()
    {
        $new_value = [3,10];
        foreach($new_value as $value){
            $cart = Cart::factory()->create();
            $product = Product::factory()->create();
            $cart_item = CartItem::create([
                'product_id'=>$product->id,
                'cart_id'=>$cart->id,
                'quantity'=>5,
                'price'=>$product->price,
                'total'=>5*$product->price
            ]);

            $response = $this->put('/api/cart-items/',[
                'uuid'=>$cart->uuid,
                'product_id'=>$product->id,
                'new_value'=>$value
            ]);
            $cart_item = CartItem::where('id',$cart_item->id)->first();
            $this->assertEquals($cart_item->quantity,$value);
            $response->assertStatus(201);
        }
    }

    public function test_cart_item_change_value_cart_error(){

        $value = 3;

        $cart = Cart::factory()->create();

        $product = Product::factory()->create();

        $cart_item = CartItem::create([
            'product_id'=>$product->id,
            'cart_id'=>$cart->id,
            'quantity'=>5,
            'price'=>$product->price,
            'total'=>5*$product->price
        ]);

        $response = $this->put('/api/cart-items/',[
            'uuid'=>'123',
            'product_id'=>$product->id,
            'new_value'=>$value
        ]);

        $response->assertStatus(404)->assertJsonStructure([
            'message',
        ]);
    }


    public function test_cart_item_change_value_product_error(){

        $value = 3;

        $cart = Cart::factory()->create();

        $product = Product::factory()->create();

        $cart_item = CartItem::create([
            'product_id'=>$product->id,
            'cart_id'=>$cart->id,
            'quantity'=>5,
            'price'=>$product->price,
            'total'=>5*$product->price
        ]);

        $response = $this->put('/api/cart-items/',[
            'uuid'=>'123',
            'product_id'=>999999999,
            'new_value'=>$value
        ]);

        $response->assertStatus(404)->assertJsonStructure([
            'message',
        ]);
    }

    public function test_cart_item_delete(){

        $cart = Cart::factory()->create();

        $product = Product::factory()->create();

        $cart_item = CartItem::create([
            'product_id'=>$product->id,
            'cart_id'=>$cart->id,
            'quantity'=>5,
            'price'=>$product->price,
            'total'=>5*$product->price
        ]);

        $response = $this->delete('/api/cart-items/'.$cart_item->id);
        $response->assertStatus(200);
    }

    public function test_cart_item_delete_error(){

        $response = $this->delete('/api/cart-items/999999999');
        $response->assertStatus(404);
    }
}
