<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiOrderTest extends TestCase
{
    use WithFaker;

    public function test_order_create()
    {
        $cart = Cart::factory()->create();

        $products = Product::factory()->count(3)->create()->each(function($product){
            $product->categories()->attach(ProductCategory::where('id','=',1)->first());
        });
        $cart_items = [];
        for($i=0;$i<3;$i++){
            $qty = $this->faker->numberBetween(3,5);
            $cart_items[]= CartItem::create([
                'product_id'=>$products[$i]->id,
                'cart_id'=>$cart->id,
                'quantity'=>$qty,
                'price'=>$products[0]->price,
                'total'=>$qty*$products[0]->price
            ]);
        }

        $response = $this->post('/api/orders',[
            'uuid'=>$cart->uuid,
            'cart_items'=>[
                $cart_items[0]->id,
                $cart_items[1]->id
            ],
            'email'=>$this->faker->unique()->email
        ]);
        $response->assertStatus(201);
    }

    public function test_order_create_no_email()
    {
        $cart = Cart::factory()->create();

        $products = Product::factory()->count(3)->create()->each(function($product){
            $product->categories()->attach(ProductCategory::where('id','=',1)->first());
        });
        $cart_items = [];
        for($i=0;$i<3;$i++){
            $qty = $this->faker->numberBetween(3,5);
            $cart_items[]= CartItem::create([
                'product_id'=>$products[$i]->id,
                'cart_id'=>$cart->id,
                'quantity'=>$qty,
                'price'=>$products[0]->price,
                'total'=>$qty*$products[0]->price
            ]);
        }

        $response = $this->post('/api/orders',[
            'uuid'=>$cart->uuid,
            'cart_items'=>[
                $cart_items[0]->id,
                $cart_items[1]->id
            ],
        ]);
        $response->assertStatus(401)->assertJsonStructure(
            [
                'message',
            ]
        );
    }


    public function test_order_create_auth()
    {

        $user = User::factory()->create();
        $token = $user->createToken('myapptoken')->plainTextToken;
        $cart = Cart::factory()->create();

        $products = Product::factory()->count(3)->create()->each(function($product){
            $product->categories()->attach(ProductCategory::where('id','=',1)->first());
        });
        $cart_items = [];
        for($i=0;$i<3;$i++){
            $qty = $this->faker->numberBetween(3,5);
            $cart_items[]= CartItem::create([
                'product_id'=>$products[$i]->id,
                'cart_id'=>$cart->id,
                'quantity'=>$qty,
                'price'=>$products[0]->price,
                'total'=>$qty*$products[0]->price
            ]);
        }

        $response = $this->withHeaders([
            'Accept'=>'application/json',
            'Authorization'=>'Bearer '.$token
        ])
            ->post('/api/orders',[
            'uuid'=>$cart->uuid,
            'cart_items'=>[
                $cart_items[0]->id,
                $cart_items[1]->id
            ],
        ]);
        $content = json_decode($response->content());

        $this->assertEquals($user->id,$content->data->user_id);
        $response->assertStatus(201)->assertJsonStructure(
            [
                'message',
                'data'=>[
                    'uuid',
                    'email',
                    'user_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]
        );
    }

    public function test_get_user_orders()
    {
        $user = User::factory()->create();
        $token = $user->createToken('myapptoken')->plainTextToken;


        $products = Product::factory()->count(3)->create()->each(function($product){
            $product->categories()->attach(ProductCategory::where('id','=',1)->first());
        });

        for($z=0;$z<3;$z++){
            $order = Order::create([
                'uuid'=>Str::uuid()->toString(),
                'email'=>$user->email,
                'user_id'=>$user->id
            ]);
            for($i=0;$i<$this->faker->numberBetween(3,5);$i++){
                $create_details = OrderDetails::create([
                    'order_id'=>$order->id,
                    'quantity'=>$qty=$this->faker->numberBetween(3,8),
                    'price'=>$price=$this->faker->numberBetween(100,1000),
                    'total'=>$qty*$price,
                    'product_id'=>$this->faker->randomElement($products)->id
                ]);
            }
        }

        $response = $this->withHeaders([
            'Accept'=>'application/json',
            'Authorization'=>'Bearer '.$token
        ])->get('/api/orders');

        $response->assertStatus(200)->assertJsonStructure(
            [
                'data'=>[[
                    'id',
                    'uuid',
                    'email',
                    'created_at',
                ],
                ]
            ]
        );
    }
}
