<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CartItemController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CartItem  $cartItem
     * @return \Illuminate\Http\Response
     */
    public function show(CartItem $cartItem)
    {
        //
    }

    /**
     * @OA\Post(
     * path="/api/cart-items",
     * summary="update cart item",
     * description="update cart item",
     * operationId="cartItemUpdate",
     * tags={"Cart items"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="uuid", type="string", format="string", example="06424b27-3f90-4cb9-b32c-a7a0e23d834a"),
     *       @OA\Property(property="product_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="new_value", type="integer", format="integer", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *    response=404,
     *    description="cart not found OR cart item not found",
     *     ),
     *  @OA\Response(
     *    response=200,
     *    description="successfully updated",     *
     *     )
     * ),
     */
    public function update(CartItemRequest $request)
    {
        $cart = Cart::where('uuid','=',$request->post('uuid'))->first();

        if(empty($cart)){
            return response([
                'message'=>'cart not found'
            ],404);
        }

        $cart_item = CartItem::where('cart_id','=',$cart->id)->
                               where('product_id','=',$request->post('product_id'))->
                                first();
        if(empty($cart_item)){
            return response([
                'message'=>'cart item not found'
            ],404);
        }

        $cart_item->quantity = $request->post('new_value');
        $cart_item->total = $request->post('new_value')*$cart_item->price;
        $cart_item->save();
        return response([
            'message'=>'successfully updated',
            ],201);
    }

    /**
     * @OA\Delete(
     * path="/api/cart-items/{id}",
     * summary="delete cart item",
     * description="delete cart item",
     * operationId="cartItemDelete",
     * tags={"Cart items"},
     * @OA\RequestBody(
     *    description="Pass user credentials",
     * ),
     * @OA\Response(
     *    response=404,
     *    description="cart item not found",
     *     ),
     *  @OA\Response(
     *    response=200,
     *    description="successfully delete",
     *     ),
     *  @OA\Response(
     *    response=400,
     *    description="delete error",
     *     )
     * ),
     */
    public function destroy($id)
    {
        $cart_item = CartItem::where('id',$id)->first();
        if(empty($cart_item)){
            return response(['message'=>'cart item not found'],404);
        }

        $res = $cart_item->delete();
        if($res){
            return response([],200);
        }
        return response(['message'],400);
    }

}
