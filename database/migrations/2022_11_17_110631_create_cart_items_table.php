<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('quantity');
            $table->float('price');
            $table->float('total');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('cart_id');
            $table->foreign('cart_id', 'cart_id_fk_1396941')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('product_id', 'product_id_fk_13969241')->references('id')->on('products')->onDelete('cascade');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}
