<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddSortColumnToTreeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_categories', function (Blueprint $table) {
            $table->bigInteger('sort')->unsigned()->nullable(false)->default(0)->index()->comment('順序')->after('name');
        });
        Schema::table('online_categories', function (Blueprint $table) {
            $table->bigInteger('sort')->unsigned()->nullable(false)->default(0)->index()->comment('順序')->after('name');
        });
        Schema::table('online_tags', function (Blueprint $table) {
            $table->bigInteger('sort')->unsigned()->nullable(false)->default(0)->index()->comment('順序')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('help_categories', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
        Schema::table('online_categories', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
        Schema::table('online_tags', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
}
