<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsUsedSameStylingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items_used_same_stylings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('items.id');
            $table->bigInteger('styling_id')->unsigned()->nullable(false)->index()->comment('stylings.id');
            $table->bigInteger('used_item_id')->unsigned()->nullable(false)->index()->comment('items.id（使用されている商品）');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('styling_id')->references('id')->on('stylings')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('used_item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items_used_same_stylings');
    }
}
