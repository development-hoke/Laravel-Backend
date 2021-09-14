<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemOnlineCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_online_categories', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('items.id');
            $table->bigInteger('online_category_id')->unsigned()->nullable(false)->index()->comment('online_categories.id');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('online_category_id')->references('id')->on('online_categories')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_online_categories');
    }
}
