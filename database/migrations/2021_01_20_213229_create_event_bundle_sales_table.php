<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventBundleSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_bundle_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id')->nullable(false)->comment('events.id');
            $table->unsignedInteger('count')->nullable(false)->comment('数量');
            $table->unsignedDecimal('rate', 8, 2)->nullable(false)->comment('割引率');
            $table->timestamps();
            $table->foreign('event_id')->references('id')->on('events')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_bundle_sales');
    }
}
