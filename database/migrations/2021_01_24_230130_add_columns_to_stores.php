<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToStores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('title')->nullable(true)->comment('屋号')->after('name');
            $table->string('open_time')->nullable(true)->comment('開店時刻')->after('location');
            $table->string('close_time')->nullable(true)->comment('閉店時刻')->after('open_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('open_time');
            $table->dropColumn('close_time');
        });
    }
}
