<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActionTextToAdminLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_logs', function (Blueprint $table) {
            $table->string('action_text', 255)->nullable(false)->comment('動作テキスト')->after('action');
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
            $table->dropColumn('action_text');
        });
    }
}
