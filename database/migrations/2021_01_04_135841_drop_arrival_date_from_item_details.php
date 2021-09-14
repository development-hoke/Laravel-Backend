<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropArrivalDateFromItemDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_details', function (Blueprint $table) {
            $table->dropColumn('arrival_date');
        });
        Schema::table('item_detail_identifications', function (Blueprint $table) {
            $table->dateTime('arrival_date')->nullable(false)->comment('入荷日')->after('latest_stock_added_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_details', function (Blueprint $table) {
            $table->date('arrival_date')->nullable(false)->comment('入荷日')->after('sort');
        });
        Schema::table('item_detail_identifications', function (Blueprint $table) {
            $table->dropColumn('arrival_date');
        });
    }
}
