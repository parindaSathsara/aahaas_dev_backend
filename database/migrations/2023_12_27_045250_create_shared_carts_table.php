<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shared_carts', function (Blueprint $table) {
            $table->id();
            $table->integer('cart_id');
            $table->integer('customer_id');
            $table->timestamps();
            $table->foreign('cart_id')->references('cart_id')->on('tbl_carts')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shared_carts');
    }
};
