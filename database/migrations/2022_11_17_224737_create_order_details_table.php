<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->float('price');
            $table->float('total');
            $table->unsignedInteger('product_id');
            $table->bigInteger('order_id')->unsigned();
            $table->foreign('order_id', 'order_id_fk_13969')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id', 'product_id_fk_139692413')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
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
        Schema::dropIfExists('order_details');
    }
}
