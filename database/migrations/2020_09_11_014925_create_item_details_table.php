<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_details', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('items.id');
            $table->bigInteger('color_id')->unsigned()->nullable(false)->comment('color.id');
            $table->bigInteger('size_id')->unsigned()->nullable(false)->comment('size.id');
            $table->unsignedInteger('jan_code')->nullable(false)->comment('JANコード');
            $table->unsignedInteger('ec_stock')->nullable(false)->comment('EC在庫数');
            $table->unsignedInteger('stock')->nullable(false)->comment('店舗在庫数');
            $table->unsignedInteger('sort')->nullable(false)->default(100)->comment('表示順');
            $table->date('arrival_date')->nullable(false)->comment('入荷日');
            $table->boolean('status')->nullable()->default(\App\Enums\Common\Status::Unpublished)->comment('公開ステータス');
            $table->datetime('status_change_date')->nullable(false)->comment('公開ステータス切替日時');
            $table->boolean('redisplay_requested')->nullable(false)->default(true)->comment('再入荷リクエスト');
            $table->datetime('last_sales_date')->nullable(true)->comment('前回販売日時');
            $table->unsignedInteger('reservable_stock')->nullable(true)->comment('予約可能在庫数');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('color_id')->references('id')->on('colors')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('size_id')->references('id')->on('sizes')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_details');
    }
}
