<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_detail_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('log_memo')->nullable(true)->comment('ログメモ（変更理由等を保存）');
            $table->bigInteger('order_detail_id')->unsigned()->nullable(false)->index()->comment('');
            $table->bigInteger('order_id')->unsigned()->nullable(false)->index()->comment('');
            $table->bigInteger('item_detail_individual_id')->unsigned()->nullable(false)->index()->comment('');
            $table->unsignedInteger('retail_price')->nullable(false)->comment('上代');
            $table->unsignedInteger('amount')->nullable(false)->default(1)->comment('購入数');
            $table->decimal('discount_rate')->nullable(true)->comment('値引率');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('order_detail_id')->references('id')->on('order_details')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_detail_logs');
    }
}
