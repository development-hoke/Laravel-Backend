<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignKeyFromOrderLogTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropForeign('order_logs_order_id_foreign');
            $table->dropIndex('order_logs_member_id_index');
            $table->dropIndex('order_logs_code_index');
        });
        Schema::table('order_address_logs', function (Blueprint $table) {
            $table->dropIndex('order_address_logs_order_id_foreign');
            $table->dropIndex('order_address_logs_pref_id_foreign');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->dropIndex('order_detail_logs_order_id_index');
            $table->dropIndex('order_detail_logs_item_detail_id_index');
        });
        Schema::table('order_detail_unit_logs', function (Blueprint $table) {
            $table->dropIndex('order_detail_unit_logs_order_detail_id_index');
            $table->dropIndex('order_detail_unit_logs_item_detail_identification_id_index');
        });
        Schema::table('order_discount_logs', function (Blueprint $table) {
            $table->dropForeign('order_discount_logs_order_discount_id_foreign');
        });
        Schema::table('order_used_coupon_logs', function (Blueprint $table) {
            $table->dropForeign('order_used_coupon_logs_order_used_coupon_id_foreign');
        });
        Schema::table('order_credit_logs', function (Blueprint $table) {
            $table->dropIndex('order_credit_logs_order_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_logs', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
            $table->index('member_id');
            $table->index('code');
        });
        Schema::table('order_address_logs', function (Blueprint $table) {
            $table->index('order_id', 'order_address_logs_order_id_foreign');
            $table->index('pref_id', 'order_address_logs_pref_id_foreign');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('item_detail_id');
        });
        Schema::table('order_detail_unit_logs', function (Blueprint $table) {
            $table->index('order_detail_id');
            $table->index('item_detail_identification_id');
        });
        Schema::table('order_discount_logs', function (Blueprint $table) {
            $table->foreign('order_discount_id')->references('id')->on('order_discounts')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::table('order_used_coupon_logs', function (Blueprint $table) {
            $table->foreign('order_used_coupon_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::table('order_credit_logs', function (Blueprint $table) {
            $table->index('order_id');
        });
    }
}
