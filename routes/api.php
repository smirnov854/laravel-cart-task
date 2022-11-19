<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::fallback(function (){
    abort(404, 'API resource not found');
});


Route::group(['middleware'=>['auth:sanctum']], function () {
    Route::get('logout', [AuthController::class,'logout']);
    Route::get('orders',[OrderController::class,'index']);
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');

});

Route::post('/get-product-by-slug',[ProductController::class,'getBySlug']);
Route::post('/get-product-by-filters',[ProductController::class,'getByFilter']);
Route::get('products',[ProductController::class,'index']);

Route::post('/add-to-cart',[CartController::class,'addToCart']);


Route::post('categorys',[ProductCategoryController::class,'addToCart']);
Route::get('categorys',[ProductCategoryController::class,'index']);
Route::post('carts',[CartController::class,'store']);
Route::post('get-cart-content',[CartController::class,'getCartContent']);
Route::post('orders',[OrderController::class,'store']);


Route::put('/cart-items',[CartItemController::class,'update']);
Route::delete('/cart-items/{id}',[CartItemController::class,'destroy']);


//Route::resource('categorys',ProductCategoryController::class);
//Route::resource('products',ProductController::class);
//Route::resource('carts',CartController::class);

//Route::resource('cart-items',CartItemController::class);








