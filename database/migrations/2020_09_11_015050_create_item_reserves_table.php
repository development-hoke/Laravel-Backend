<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReservesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_reserves', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('items.id');
            $table->boolean('status')->nullable(false)->default(false)->comment('予約販売ステータス');
            $table->datetime('period_from')->nullable(false)->comment('期間(from)');
            $table->datetime('period_to')->nullable(false)->comment('期間(to)');
            $table->boolean('normal_sale_after_period')->nullable(false)->default(false)->comment('期間終了後の通常販売');
            $table->unsignedInteger('member_price')->nullable(false)->comment('会員価格');
            $table->decimal('point_rate')->nullable(false)->comment('ポイント付与率');
            $table->unsignedInteger('reserve_price')->nullable(false)->comment('予約販売価格');
            $table->unsignedInteger('limited_stock_threshold')->nullable(false)->comment('予約在庫僅少表示閾値');
            $table->unsignedInteger('out_of_stock_threshold')->nullable(false)->comment('予約在庫切れメール閾値');
            $table->datetime('expected_arrival_date')->nullable(false)->comment('入荷予定日');
            $table->text('note')->nullable(false)->comment('予約商品の注意書き');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

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
        Schema::dropIfExists('item_reserves');
    }
}
