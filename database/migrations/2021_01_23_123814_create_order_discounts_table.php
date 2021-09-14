<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_discounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('orderable_type', 255)->nullable(true)->comment('受注種別 (Order|OrderDetail)');
            $table->unsignedBigInteger('orderable_id')->nullable(false)->comment('受注ID|受注詳細ID');
            $table->unsignedInteger('applied_price')->nullable(false)->comment('適用済み割引金額');
            $table->unsignedInteger('unit_applied_price')->nullable(true)->comment('1個あたりの適用済み割引金額(orderable_type=OrderDetailの場合のみ)');
            $table->unsignedTinyInteger('type')->nullable(false)->comment('種別');
            $table->unsignedTinyInteger('method')->nullable(false)->comment('計算方法 1:定額 2:定率');
            $table->unsignedSmallInteger('priority')->nullable(false)->default(1000)->comment('適用優先度');
            $table->unsignedInteger('discount_price')->nullable(true)->comment('割引金額');
            $table->decimal('discount_rate', 8, 2)->nullable(true)->comment('割引率');
            $table->string('discountable_type', 255)->nullable(true)->comment('割引元種別 (Event, ItemReserve, OrderUsedCouponなど)');
            $table->unsignedBigInteger('discountable_id')->nullable(true)->comment('割引元ID');
            $table->unsignedBigInteger('update_staff_id')->nullable(true)->comment('更新者スタッフID');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->index(['orderable_type', 'orderable_id']);
            $table->index(['discountable_type', 'discountable_id']);
        });

        Schema::create('order_discount_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_discount_id')->nullable(false)->comment('受注割引ID');
            $table->string('orderable_type', 255)->nullable(true)->comment('受注種別 (Order|OrderDetail)');
            $table->unsignedBigInteger('orderable_id')->nullable(false)->comment('受注ID|受注詳細ID');
            $table->unsignedInteger('applied_price')->nullable(false)->comment('適用済み割引金額');
            $table->unsignedInteger('unit_applied_price')->nullable(true)->comment('1個あたりの適用済み割引金額(orderable_type=OrderDetailの場合のみ)');
            $table->unsignedTinyInteger('type')->nullable(false)->comment('種別');
            $table->unsignedTinyInteger('method')->nullable(false)->comment('計算方法 1:定額 2:定率');
            $table->unsignedSmallInteger('priority')->nullable(false)->default(1000)->comment('適用優先度');
            $table->unsignedInteger('discount_price')->nullable(true)->comment('割引金額');
            $table->decimal('discount_rate', 8, 2)->nullable(true)->comment('割引率');
            $table->string('discountable_type', 255)->nullable(true)->comment('割引元種別 (Event, ItemReserve, OrderUsedCouponなど)');
            $table->unsignedBigInteger('discountable_id')->nullable(true)->comment('割引元ID');
            $table->unsignedBigInteger('update_staff_id')->nullable(true)->comment('更新者スタッフID');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('order_discount_id')->references('id')->on('order_discounts')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_discount_logs');
        Schema::dropIfExists('order_discounts');
    }
}
