<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemOnlineTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_online_tags', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('items.id');
            $table->bigInteger('online_tag_id')->unsigned()->nullable(false)->index()->comment('online_tags.id');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('online_tag_id')->references('id')->on('online_tags')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_online_tags');
    }
}
