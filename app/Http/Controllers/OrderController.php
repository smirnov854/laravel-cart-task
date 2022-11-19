<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderCollection;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new OrderCollection(Order::where('user_id','=',auth('sanctum')->user()->id)->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {
        $cart = Cart::where('uuid',$request->post('uuid'))->first();
        if(empty($cart)){
            return response(['message'=>'Cart not found'],404);
        }
        $user_id = null;
        $email = $request->post('email');
        if(auth('sanctum')->check()){
            $user_id = auth('sanctum')->user()->id;
            $email = auth('sanctum')->user()->email;
        }
        if(empty($request->post('email')) && empty($user_id)){
            return response(['message'=>'need to add email data or be authenticated'],401);
        }
        $order = Order::create([
            'uuid'=>Str::uuid()->toString(),
            'email'=>$email,
            'user_id'=>$user_id
        ]);
        foreach($request->post('cart_items') as $row){
            $cart_item = CartItem::where('id','=',$row)->first();
            $create_details = OrderDetails::create([
                'order_id'=>$order->id,
                'quantity'=>$cart_item->quantity,
                'price'=>$cart_item->price,
                'total'=>$cart_item->total,
                'product_id'=>$cart_item->product_id
            ]);
            if($create_details){
                $cart_item->delete();
            }
        }
        return response([
            'message'=>'Order created',
            'data'=>$order
        ],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
