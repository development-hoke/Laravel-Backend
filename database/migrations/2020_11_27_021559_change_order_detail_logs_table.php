<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOrderDetailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->dropColumn('item_detail_individual_id');
            $table->bigInteger('item_detail_id')->unsigned()->nullable(false)->index()->comment('商品詳細ID')->after('order_id');
            $table->integer('tax')->nullable(false)->comment('消費税')->after('discount_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->bigInteger('item_detail_individual_id')->unsigned()->nullable(false)->index()->comment('item_detail_individuals.id')->after('order_id');
            $table->dropColumn('item_detail_id');
            $table->dropColumn('tax');
        });
    }
}
