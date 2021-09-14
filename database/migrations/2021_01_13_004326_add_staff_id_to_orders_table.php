<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaffIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('update_staff_id')->nullable(true)->comment('更新者スタッフID')->after('shop_memo');
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->unsignedBigInteger('update_staff_id')->nullable(true)->comment('更新者スタッフID')->after('tax_rate_id');
        });
        Schema::table('order_detail_units', function (Blueprint $table) {
            $table->unsignedBigInteger('update_staff_id')->nullable(true)->comment('更新者スタッフID')->after('amount');
        });
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('update_staff_id')->nullable(true)->comment('更新者スタッフID')->after('building');
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
            $table->dropColumn('update_staff_id');
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('update_staff_id');
        });
        Schema::table('order_detail_units', function (Blueprint $table) {
            $table->dropColumn('update_staff_id');
        });
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->dropColumn('update_staff_id');
        });
    }
}
