<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderUsedCouponLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_used_coupon_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_used_coupon_id')->nullable(false)->comment('使用済みクーポン管理ID');
            $table->unsignedBigInteger('order_id')->nullable(false)->comment('受注ID');
            $table->unsignedBigInteger('coupon_id')->nullable(false)->comment('クーポンID');
            $table->unsignedBigInteger('update_staff_id')->nullable(true)->comment('更新者スタッフID');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('order_used_coupon_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_used_coupon_logs');
    }
}
