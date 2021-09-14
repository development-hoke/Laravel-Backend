<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoutePathColumnToAdminLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_logs', function (Blueprint $table) {
            $table->dropUnique('admin_logs_page_unique');
            $table->string('url', 255)->nullable(false)->comment('URL')->after('type');
            $table->renameColumn('page', 'action');
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
            $table->dropColumn('url');
            $table->renameColumn('action', 'page');
            $table->unique('page');
        });
    }
}
