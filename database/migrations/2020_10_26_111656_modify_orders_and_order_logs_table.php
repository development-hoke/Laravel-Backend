<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOrdersAndOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // クーポンの修正
            $table->dropColumn('coupon_code');
            $table->json('use_coupon_ids')->nullable(true)->comment('使用クーポン')->after('fee');
            // discount_rateの削除
            $table->dropColumn('discount_rate');
            $table->dropColumn('discount_memo');
        });
        Schema::table('order_logs', function (Blueprint $table) {
            // クーポンの修正
            $table->dropColumn('coupon_code');
            $table->json('use_coupon_ids')->nullable(true)->comment('使用クーポン')->after('fee');
            // discount_rateの削除
            $table->dropColumn('discount_rate');
            $table->dropColumn('discount_memo');
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
            $table->dropColumn('use_coupon_ids');
            $table->string('coupon_code', 32)->nullable(true)->comment('使用クーポン')->after('fee');
            $table->decimal('discount_rate')->nullable(true)->comment('値引率')->after('price');
            $table->json('discount_memo')->nullable(true)->comment('適用された割引の内訳')->after('discount_rate');
        });
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropColumn('use_coupon_ids');
            $table->string('coupon_code', 32)->nullable(true)->comment('使用クーポン')->after('fee');
            $table->decimal('discount_rate')->nullable(true)->comment('値引率')->after('price');
            $table->json('discount_memo')->nullable(true)->comment('適用された割引の内訳')->after('discount_rate');
        });
    }
}
