<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImportedStoreStockColumnToTempStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_stocks', function (Blueprint $table) {
            $table->boolean('imported_store_stock')->default(false)->comment('店舗在庫を読み込み済みか')->after('imported');
            $table->boolean('imported_store_stock_by_jan')->default(false)->comment('JAN単位の店舗在庫を読み込み済みか')->after('imported_store_stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_stocks', function (Blueprint $table) {
            $table->dropColumn('imported_store_stock');
            $table->dropColumn('imported_store_stock_by_jan');
        });
    }
}
