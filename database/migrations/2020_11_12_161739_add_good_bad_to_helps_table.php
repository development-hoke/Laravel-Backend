<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoodBadToHelpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('helps', function (Blueprint $table) {
            $table->unsignedInteger('good')->nullable(false)->default(0)->after('is_faq')->comment('役に立った');
            $table->unsignedInteger('bad')->nullable(false)->default(0)->after('good')->comment('役に立たなかった');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('helps', function (Blueprint $table) {
            $table->dropColumn('good');
            $table->dropColumn('bad');
        });
    }
}
