<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('event_type')->nullable(false)->comment('イベントタイプ')->after('order_id');
            $table->unsignedBigInteger('staff_id')->nullable(true)->comment('スタッフID')->after('event_type');
            $table->json('diff_json')->nullable(true)->comment('差分')->after('staff_id');
            $table->foreign('staff_id')->references('id')->on('staffs')->onUpdate('cascade')->onDelete('restrict');
            $table->dropUnique('order_logs_delivery_token_unique');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('event_type')->nullable(false)->comment('イベントタイプ')->after('order_detail_id');
            $table->unsignedBigInteger('staff_id')->nullable(true)->comment('スタッフID')->after('event_type');
            $table->json('diff_json')->nullable(true)->comment('差分')->after('staff_id');
            $table->foreign('staff_id')->references('id')->on('staffs')->onUpdate('cascade')->onDelete('restrict');
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
            $table->dropForeign('order_logs_staff_id_foreign');
            $table->dropColumn('event_type');
            $table->dropColumn('staff_id');
            $table->dropColumn('diff_json');
            $table->unique('delivery_token');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->dropForeign('order_detail_logs_staff_id_foreign');
            $table->dropColumn('event_type');
            $table->dropColumn('staff_id');
            $table->dropColumn('diff_json');
        });
    }
}
