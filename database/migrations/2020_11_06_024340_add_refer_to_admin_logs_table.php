<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferToAdminLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_logs', function (Blueprint $table) {
            $table->string('referer', 255)->nullable(false)->comment('リファラ')->after('ip');
            $table->string('action', 255)->nullable(false)->comment('動作')->change();
            $table->string('ip', 15)->nullable(false)->comment('IPアドレス')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_logs', function (Blueprint $table) {
            $table->dropColumn('referer');
        });
    }
}
