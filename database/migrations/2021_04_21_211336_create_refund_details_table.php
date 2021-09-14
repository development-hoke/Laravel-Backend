<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->nullable(false)->comment('orders.id');
            $table->string('refundable_type')->nullable(true)->comment('返金対象タイプ');
            $table->unsignedBigInteger('refundable_id')->nullable(true)->comment('返金対象ID');
            $table->unsignedInteger('unit_price')->nullable(false)->comment('返金単位価格');
            $table->unsignedInteger('amount')->nullable(false)->comment('数量');
            $table->unsignedInteger('update_staff_id')->nullable(true)->comment('更新者staffs.id');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
            $table->index(['refundable_id', 'refundable_type']);
            $table->index('update_staff_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refund_details');
    }
}
