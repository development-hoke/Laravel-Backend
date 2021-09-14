<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemDetailStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_detail_stores', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('item_detail_id')->nullable(false)->comment('item_details.id');
            $table->unsignedBigInteger('store_id')->nullable(false)->comment('stores.id');
            $table->unsignedInteger('stock')->nullable(false)->comment('店舗在庫数');
            $table->timestamps();

            $table->foreign('item_detail_id')->references('id')->on('item_details')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('store_id')->references('id')->on('stores')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_detail_stores');
    }
}
