<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnsOfClosedMarkets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('closed_markets', function (Blueprint $table) {
            $table->dropColumn('url');
            $table->unsignedInteger('stock')->nullable(false)->comment('確保在庫')->after('num');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('closed_markets', function (Blueprint $table) {
            $table->dropColumn('stock');
            $table->string('url', 255)->nullable(false)->unique()->comment('URL')->after('item_detail_id');
        });
    }
}
