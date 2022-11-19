<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductProductAddParams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_product_additional_params', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
            $table->foreign('product_id', 'product_id_fk_13961941')->references('id')->on('products')->onDelete('cascade');
            $table->bigInteger('product_additional_params_id')->unsigned();
            $table->foreign('product_additional_params_id', 'product_add_params_id_fk_1396941')->references('id')->on('product_additional_params')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
