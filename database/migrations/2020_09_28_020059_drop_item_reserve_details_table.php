<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropItemReserveDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('item_reserve_details');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('item_reserve_details', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('item_reserves.id');
            $table->bigInteger('color_id')->unsigned()->nullable(false)->comment('');
            $table->bigInteger('size_id')->unsigned()->nullable(false)->comment('');
            $table->unsignedInteger('stock')->nullable(false)->comment('予約在庫数');
            $table->unsignedInteger('sort')->nullable(false)->default(100)->comment('表示順');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('color_id')->references('id')->on('colors')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('size_id')->references('id')->on('sizes')->onUpdate('cascade')->onDelete('restrict');
        });
    }
}
