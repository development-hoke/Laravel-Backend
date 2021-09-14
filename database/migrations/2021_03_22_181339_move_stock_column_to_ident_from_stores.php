<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveStockColumnToIdentFromStores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_detail_identifications', function (Blueprint $table) {
            $table->unsignedInteger('store_stock')->nullable(false)->comment('店舗在庫数')->after('old_jan_code');
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
            $table->dropColumn('store_stock');
        });
    }
}
