<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemDetailIdentificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_detail_identifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('item_detail_id')->unsigned()->nullable(false)->comment('item_details.id');
            $table->string('jan_code', 30)->nullable(false)->comment('JANコード');
            $table->unsignedInteger('ec_stock')->nullable(false)->comment('EC在庫数');
            $table->unsignedInteger('reservable_stock')->nullable(true)->comment('予約可能在庫数');
            $table->integer('dead_inventory_days')->nullable(true)->comment('不動日数');
            $table->integer('slow_moving_inventory_days')->nullable(true)->comment('滞留日数');
            $table->integer('latest_added_stock')->nullable(true)->comment('最新の在庫追加数 (item_detail_records.stockの最新の値)');
            $table->dateTime('latest_stock_added_at')->nullable(true)->comment('最新の在庫追加日時');
            $table->timestamps();
            $table->foreign('item_detail_id')->references('id')->on('item_details')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_detail_identifications');
    }
}
