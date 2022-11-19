<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * @OA\Post(
     * path="/api/carts",
     * summary="Carts create",
     * description="Create cart",
     * operationId="cartCreate",
     * tags={"Cart"},
     * @OA\Response(
     *    response=201,
     *    description="Cart created",
     *     ),
     * ),
     */
    public function store()
    {
        $new_cart = Cart::create([
            'uuid'=>Str::uuid()->toString(),
        ]);
        return response([
            'message'=>'Cart created',
            'cart_uuid'=>$new_cart->uuid
            ],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        //
    }

    /**
     * @OA\Post(
     * path="/api/add-to-cart",
     * summary="Add to cart",
     * description="Add item to cart",
     * operationId="addItemToCart",
     * tags={"Cart"},
     * @OA\Response(
     *    response=201,
     *    description="Cart exist. Item added",
     *     ),
     *  @OA\Response(
     *    response=404,
     *    description="Cart not found Or Product not found",
     *     ),
     *
     *@OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="uuid", type="string", format="string", example="06424b27-3f90-4cb9-b32c-a7a0e23d834a"),
     *       @OA\Property(property="quantity", type="integer", format="integer", example="1"),
     *       @OA\Property(property="product_id", type="integer", format="integer", example="1"),
     *    ),
     * ))
     */

    public function getCartContent(Request $request){
        $request->validate([
            "uuid"=>'required|string',
        ]);
        $content = CartItem::select('cart_items.*')->
                             leftJoin('carts','carts.id','=','cart_items.cart_id')->
                             where('carts.uuid',$request->post('uuid'))->get();
        return response(['data'=>$content],200);
    }

    public function addToCart(AddToCartRequest $request){

        $product = Product::where('id','=',$request->all('product_id'))->first();

        if(empty($product)){
            return response([
                'message'=>'product not found'
            ],404);
        }

        $cart = Cart::where('uuid','=',$request->uuid)->first();

        if(empty($cart)){
            return response([
                'message'=>'cart not found'
            ],404);
        }

        $product_item = CartItem::where('cart_id','=',$cart->id)->
                                  where('product_id','=',$request->post('product_id'))->
                                  first();

        if(!empty($product_item)){
            $product_item->quantity++;
            $product_item->total = $product_item->quantity * $product_item->price;
            $product_item->save();
        }else{
            CartItem::create([
                'product_id'=>$request->product_id,
                'cart_id'=>$cart->id,
                'quantity'=>$request->quantity,
                'price'=>$product->price,
                'total'=>$request->quantity*$product->price
            ]);
        }
        return response([
            'message'=>'Cart exist. Item added'
        ],201);
    }
}
