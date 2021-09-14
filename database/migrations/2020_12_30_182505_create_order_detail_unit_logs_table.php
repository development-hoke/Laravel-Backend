<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailUnitLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_detail_unit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_detail_unit_id')->unsigned()->nullable(false)->comment('商品詳細ID');
            $table->unsignedTinyInteger('event_type')->nullable(false)->comment('イベントタイプ');
            $table->unsignedBigInteger('staff_id')->nullable(true)->comment('スタッフID');
            $table->text('diff_json')->nullable(true)->comment('差分');

            $table->bigInteger('order_detail_id')->unsigned()->nullable(false)->index()->comment('商品詳細識別ID');
            $table->bigInteger('item_detail_identification_id')->unsigned()->nullable(false)->index()->comment('商品詳細識別ID');
            $table->unsignedInteger('amount')->nullable(false)->default(1)->comment('購入数');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_detail_unit_id')->references('id')->on('order_detail_units')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_detail_unit_logs');
    }
}
