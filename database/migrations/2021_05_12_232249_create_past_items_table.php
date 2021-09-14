<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePastItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('past_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable(false)->comment('商品名');
            $table->string('old_jan_code', 13)->nullable(false)->comment('旧JANコード');
            $table->string('jan_code', 30)->nullable(false)->comment('JANコード');
            $table->string('product_number', 9)->nullable(false)->comment('事部品番');
            $table->string('maker_product_number', 255)->nullable(false)->comment('メーカーコード品番');
            $table->unsignedInteger('sort')->nullable(false)->default(100)->comment('表示順');
            $table->unsignedInteger('retail_price')->nullable(false)->comment('上代');
            $table->unsignedInteger('price')->nullable(false)->comment('販売価格');
            $table->string('image_url', 255)->nullable(false)->comment('サムネイルURL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('past_items');
    }
}
