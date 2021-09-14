<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStylingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('styling_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('styling_id')->unsigned()->nullable(false)->comment('styling.id');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->comment('item.id');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('styling_id')->references('id')->on('stylings')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('styling_items');
    }
}
