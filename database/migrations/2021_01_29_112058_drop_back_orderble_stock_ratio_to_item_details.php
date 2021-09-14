<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropBackOrderbleStockRatioToItemDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_details', function (Blueprint $table) {
            $table->dropColumn('back_orderble_stock_ratio');
            $table->dropColumn('back_orderble_min_stock_over_100');
            $table->dropColumn('back_orderble_min_stock_under_100');
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
            $table->decimal('back_orderble_stock_ratio', 8, 2)->nullable(false)->comment('取り寄せできる在庫比率')->after('item_detail_request_count');
            $table->integer('back_orderble_min_stock_over_100')->unsigned()->nullable(false)->comment('取り寄せできる取り寄せ100以上の在庫下限数量')->after('back_orderble_stock_ratio');
            $table->tinyInteger('back_orderble_min_stock_under_100')->unsigned()->nullable(false)->comment('取り寄せできる100未満の在庫下限数量')->after('back_orderble_min_stock_over_100');
        });
    }
}
