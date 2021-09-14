<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCsvColumnsToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('device_type')->nullable(false)->comment('注文ステータス')->after('shop_memo');
            $table->unsignedInteger('tax')->nullable(true)->comment('消費税')->after('changed_price');
        });
        Schema::table('order_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('device_type')->nullable(false)->comment('注文ステータス')->after('shop_memo');
            $table->unsignedInteger('tax')->nullable(true)->comment('消費税')->after('changed_price');
        });
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('email', 255)->nullable(false)->comment('メールアドレス')->after('building');
        });
        Schema::table('order_address_logs', function (Blueprint $table) {
            $table->string('email', 255)->nullable(false)->comment('メールアドレス')->after('building');
        });
        Schema::table('order_detail_units', function (Blueprint $table) {
            $table->unsignedInteger('tax')->nullable(true)->comment('消費税')->after('amount');
        });
        Schema::table('order_detail_unit_logs', function (Blueprint $table) {
            $table->unsignedInteger('tax')->nullable(true)->comment('消費税')->after('amount');
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
            $table->dropColumn('device_type');
            $table->dropColumn('tax');
        });
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropColumn('device_type');
            $table->dropColumn('tax');
        });
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->dropColumn('email');
        });
        Schema::table('order_address_logs', function (Blueprint $table) {
            $table->dropColumn('email');
        });
        Schema::table('order_detail_units', function (Blueprint $table) {
            $table->dropColumn('tax');
        });
        Schema::table('order_detail_unit_logs', function (Blueprint $table) {
            $table->dropColumn('tax');
        });
    }
}
