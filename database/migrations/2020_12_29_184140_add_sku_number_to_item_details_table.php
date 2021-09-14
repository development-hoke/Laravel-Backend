<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkuNumberToItemDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_details', function (Blueprint $table) {
            $table->dropColumn('jan_code');
            $table->dropColumn('ec_stock');
            $table->dropColumn('reservable_stock');
            $table->dropColumn('dead_inventory_days');
            $table->dropColumn('slow_moving_inventory_days');
            $table->dropColumn('latest_added_stock');
            $table->dropColumn('latest_stock_added_at');

            $table->string('sku_number', 30)->nullable(false)->unique()->comment('SKU番号')->after('size_id');
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
            $table->dropColumn('sku_number');

            $table->string('jan_code', 13)->nullable(false)->comment('JANコード')->after('size_id');
            $table->unsignedInteger('ec_stock')->nullable(false)->comment('EC在庫数')->after('jan_code');
            $table->unsignedInteger('reservable_stock')->nullable(true)->comment('予約可能在庫数')->after('last_sales_date');
            $table->integer('dead_inventory_days')->nullable(true)->comment('不動日数')->after('reservable_stock');
            $table->integer('slow_moving_inventory_days')->nullable(true)->comment('滞留日数')->after('dead_inventory_days');
            $table->integer('latest_added_stock')->nullable(true)->comment('最新の在庫追加数 (item_detail_records.stockの最新の値)')->after('item_detail_request_count');
            $table->dateTime('latest_stock_added_at')->nullable(true)->comment('最新の在庫追加日時')->after('latest_added_stock');
        });
    }
}
