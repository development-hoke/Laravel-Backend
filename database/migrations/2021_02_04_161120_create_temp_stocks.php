<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('store_id')->nullable(false)->comment('stores.id');
            $table->string('jan_code', 13)->nullable(false)->comment('JANコード')->index();
            $table->string('item_status_id', 13)->nullable(false)->comment('在庫ステータス');
            $table->boolean('imported')->default(false)->comment('取り込み済みかどうか');
            $table->timestamps();

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
        Schema::dropIfExists('temp_stocks');
    }
}
