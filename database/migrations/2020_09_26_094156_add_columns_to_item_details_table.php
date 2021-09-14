<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToItemDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_details', function (Blueprint $table) {
            $table->integer('dead_inventory_days')->nullable(true)->comment('不動日数')->after('reservable_stock');
            $table->integer('slow_moving_inventory_days')->nullable(true)->comment('滞留日数')->after('dead_inventory_days');
            $table->integer('item_detail_request_count')->nullable(true)->comment('再入荷リクエスト数 (item_detail_requestsの件数)')->after('slow_moving_inventory_days');
            $table->integer('latest_added_stock')->nullable(true)->comment('最新の在庫追加数 (item_detail_records.stockの最新の値)')->after('item_detail_request_count');
            $table->dateTime('latest_stock_added_at')->nullable(true)->comment('最新の在庫追加日時')->after('latest_added_stock');
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
            $table->dropColumn('dead_inventory_days');
            $table->dropColumn('slow_moving_inventory_days');
            $table->dropColumn('item_detail_request_count');
            $table->dropColumn('latest_added_stock');
            $table->dropColumn('latest_stock_added_at');
        });
    }
}
