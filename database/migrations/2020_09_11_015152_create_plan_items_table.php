<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_items', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('plan_id')->unsigned()->nullable(false)->index()->comment('plans.id');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('items.id');
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('plans')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::dropIfExists('plan_items');
    }
}
