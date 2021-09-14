<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDeletedAtFromMasterTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('sizes', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('terms', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('season_groups', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('prefs', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('department_groups', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('colors', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('staffs', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
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
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('divisions', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('sizes', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('terms', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('seasons', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('season_groups', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('prefs', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('department_groups', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('destinations', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('colors', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('staffs', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
    }
}
