<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOldJanToItemDetailIdentifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_detail_identifications', function (Blueprint $table) {
            $table->string('old_jan_code', 13)->nullable(true)->comment('旧JANコード')->index()->after('jan_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_detail_identifications', function (Blueprint $table) {
            $table->dropColumn('old_jan_code');
        });
    }
}
