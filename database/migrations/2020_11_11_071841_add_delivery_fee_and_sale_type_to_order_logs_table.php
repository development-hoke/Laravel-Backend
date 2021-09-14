<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @SuppressWarnings(PHPMD.LongClassName)
 */
class AddDeliveryFeeAndSaleTypeToOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_logs', function (Blueprint $table) {
            $table->unsignedInteger('delivery_fee')->nullable(false)->comment('送料')->after('delivery_hope_time');
            $table->unsignedInteger('sale_type')->nullable(true)->comment('販売種類');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropColumn('delivery_fee');
            $table->dropColumn('sale_type');
        });
    }
}
