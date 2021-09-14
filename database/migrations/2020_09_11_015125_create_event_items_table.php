<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_items', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('event_id')->unsigned()->nullable(false)->index()->comment('events.id');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('items.id');
            $table->decimal('discount_rate')->nullable(false)->comment('値引率');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_items');
    }
}
