<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvailableStockToOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_details', function (Blueprint $table) {
            $table->integer('available_stock')->nullable(true)->comment('最低有効在庫数')->after('latest_stock_added_at');
            $table->boolean('is_manually_setting_available_stock')->nullable(true)->default(false)->comment('手動設定の有無')->after('available_stock');
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
            $table->dropColumn('available_stock');
            $table->dropColumn('is_manually_setting_available_stock');
        });
    }
}
