<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUnwantedColumnsFromOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropForeign('order_logs_staff_id_foreign');
            $table->dropColumn('event_type');
            $table->dropColumn('staff_id');
            $table->dropColumn('diff_json');
            $table->dropColumn('log_memo');
            $table->unsignedBigInteger('update_staff_id')->nullable(true)->comment('更新者スタッフID')->after('shop_memo');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->dropForeign('order_detail_logs_order_detail_id_foreign');
            $table->dropForeign('order_detail_logs_order_id_foreign');
            $table->dropForeign('order_detail_logs_staff_id_foreign');
            $table->dropColumn('event_type');
            $table->dropColumn('staff_id');
            $table->dropColumn('diff_json');
            $table->dropColumn('log_memo');
            $table->unsignedBigInteger('update_staff_id')->nullable(true)->comment('更新者スタッフID')->after('tax_rate_id');
        });
        Schema::table('order_detail_unit_logs', function (Blueprint $table) {
            $table->dropForeign('order_detail_unit_logs_order_detail_unit_id_foreign');
            $table->dropColumn('event_type');
            $table->dropColumn('staff_id');
            $table->dropColumn('diff_json');
            $table->unsignedBigInteger('update_staff_id')->nullable(true)->comment('更新者スタッフID')->after('amount');
        });
        Schema::table('order_address_logs', function (Blueprint $table) {
            $table->dropForeign('order_address_logs_order_address_id_foreign');
            $table->dropForeign('order_address_logs_order_id_foreign');
            $table->dropForeign('order_address_logs_pref_id_foreign');
            $table->dropForeign('order_address_logs_staff_id_foreign');
            $table->dropColumn('event_type');
            $table->dropColumn('staff_id');
            $table->dropColumn('diff_json');
            $table->dropColumn('log_memo');
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
        Schema::table('order_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('event_type')->nullable(false)->comment('イベントタイプ')->after('order_id');
            $table->unsignedBigInteger('staff_id')->nullable(true)->comment('スタッフID')->after('event_type');
            $table->json('diff_json')->nullable(true)->comment('差分')->after('staff_id');
            $table->text('log_memo')->nullable(true)->comment('ログメモ（変更理由等を保存）')->after('diff_json');

            $table->foreign('staff_id')->references('id')->on('staffs')->onUpdate('cascade')->onDelete('restrict');
            $table->dropColumn('update_staff_id');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('event_type')->nullable(false)->comment('イベントタイプ')->after('order_detail_id');
            $table->unsignedBigInteger('staff_id')->nullable(true)->comment('スタッフID')->after('event_type');
            $table->json('diff_json')->nullable(true)->comment('差分')->after('staff_id');
            $table->text('log_memo')->nullable(true)->comment('ログメモ（変更理由等を保存）')->after('diff_json');

            $table->foreign('staff_id')->references('id')->on('staffs')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
            $table->dropColumn('update_staff_id');
        });
        Schema::table('order_detail_unit_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('event_type')->nullable(false)->comment('イベントタイプ')->after('order_detail_unit_id');
            $table->unsignedBigInteger('staff_id')->nullable(true)->comment('スタッフID')->after('event_type');
            $table->json('diff_json')->nullable(true)->comment('差分')->after('staff_id');

            $table->foreign('order_detail_unit_id')->references('id')->on('order_detail_units')->onUpdate('cascade')->onDelete('restrict');
            $table->dropColumn('update_staff_id');
        });
        Schema::table('order_address_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('event_type')->nullable(false)->comment('イベントタイプ')->after('order_address_id');
            $table->unsignedBigInteger('staff_id')->nullable(true)->comment('スタッフID')->after('event_type');
            $table->json('diff_json')->nullable(true)->comment('差分')->after('staff_id');
            $table->text('log_memo')->nullable(true)->comment('ログメモ（変更理由等を保存）')->after('diff_json');

            $table->foreign('order_address_id')->references('id')->on('order_addresses')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('pref_id')->references('id')->on('prefs')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('staff_id')->references('id')->on('staffs')->onUpdate('cascade')->onDelete('restrict');
            $table->dropColumn('update_staff_id');
        });
    }
}
