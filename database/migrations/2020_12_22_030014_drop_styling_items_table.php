<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropStylingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('styling_items');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('styling_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('coordinate_id')->unsigned()->nullable(false)->index()->comment('スタッフスタートAPIのcid');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->comment('item.id');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
            $table->unique(['coordinate_id', 'item_id']);
        });
    }
}
