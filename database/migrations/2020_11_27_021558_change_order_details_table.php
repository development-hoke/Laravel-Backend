<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign('order_details_item_detail_individual_id_foreign');
            $table->dropColumn('item_detail_individual_id');
            $table->bigInteger('item_detail_id')->unsigned()->nullable(false)->index()->comment('商品詳細ID')->after('order_id');
            $table->foreign('item_detail_id')->references('id')->on('item_details')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::table('order_details', function (Blueprint $table) {
            $table->bigInteger('item_detail_individual_id')->unsigned()->nullable(false)->index()->comment('item_detail_individuals.id')->after('order_id');
            $table->foreign('item_detail_individual_id')->references('id')->on('item_detail_individuals')->onUpdate('cascade')->onDelete('restrict');

            $table->dropForeign('order_details_item_detail_id_foreign');
            $table->dropColumn('item_detail_id');

            $table->dropColumn('tax');
        });
    }
}
