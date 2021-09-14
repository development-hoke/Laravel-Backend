<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderUsedCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_used_coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->nullable(false)->comment('受注ID');
            $table->unsignedBigInteger('coupon_id')->nullable(false)->comment('クーポンID');
            $table->text('target_order_detail_ids')->nullable(true)->comment('適用されたorder_details.id');
            $table->unsignedBigInteger('update_staff_id')->nullable(true)->comment('更新者スタッフID');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->index(['coupon_id', 'order_id']);
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
        Schema::dropIfExists('order_used_coupons');
    }
}
