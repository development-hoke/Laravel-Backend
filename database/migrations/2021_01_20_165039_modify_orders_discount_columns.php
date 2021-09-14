<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOrdersDiscountColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('use_coupon_ids');
            $table->integer('changed_price')->nullable(false)->default(0)->comment('金額に直接変更を加えた差額')->after('price');
        });
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropColumn('use_coupon_ids');
            $table->integer('changed_price')->nullable(false)->default(0)->comment('金額に直接変更を加えた差額')->after('price');
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('discount_rate');
            $table->dropColumn('tax');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->dropColumn('discount_rate');
            $table->dropColumn('tax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->json('use_coupon_ids')->nullable(true)->comment('使用クーポン')->after('fee');
        });
        Schema::table('order_logs', function (Blueprint $table) {
            $table->json('use_coupon_ids')->nullable(true)->comment('使用クーポン')->after('fee');
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->decimal('discount_rate')->nullable(true)->comment('値引率')->after('retail_price');
            $table->integer('tax')->nullable(false)->comment('消費税')->after('discount_rate');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->decimal('discount_rate')->nullable(true)->comment('値引率')->after('retail_price');
            $table->integer('tax')->nullable(false)->comment('消費税')->after('discount_rate');
        });
    }
}
