<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_detail_units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_detail_id')->unsigned()->nullable(false)->comment('商品詳細識別ID');
            $table->bigInteger('item_detail_identification_id')->unsigned()->nullable(false)->comment('商品詳細識別ID');
            $table->unsignedInteger('amount')->nullable(false)->default(1)->comment('購入数');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_detail_id')->references('id')->on('order_details')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('item_detail_identification_id')->references('id')->on('item_detail_identifications')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_detail_units');
    }
}
