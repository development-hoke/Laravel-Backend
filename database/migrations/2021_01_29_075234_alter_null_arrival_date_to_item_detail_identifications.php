<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @SuppressWarnings(PHPMD.LongClassName)
 */
class AlterNullArrivalDateToItemDetailIdentifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_detail_identifications', function (Blueprint $table) {
            $table->dateTime('arrival_date')->nullable(true)->change();
        });
        \DB::statement('UPDATE `item_detail_identifications` SET `arrival_date` = NULL WHERE `arrival_date` = \'0001-01-01 00:00:00\'');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('UPDATE `item_detail_identifications` SET `arrival_date` = \'0001-01-01 00:00:00\' WHERE `arrival_date` IS NULL');
        Schema::table('item_detail_identifications', function (Blueprint $table) {
            $table->dateTime('arrival_date')->nullable(false)->change();
        });
    }
}
