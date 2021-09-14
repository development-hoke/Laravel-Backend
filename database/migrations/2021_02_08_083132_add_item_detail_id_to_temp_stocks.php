<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemDetailIdToTempStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('temp_stocks', function (Blueprint $table) {
            $table->bigInteger('item_detail_id')->unsigned()->nullable(false)->comment('item_details.id')->after('id');
            $table->foreign('item_detail_id')->references('id')->on('item_details')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('temp_stocks', function (Blueprint $table) {
            $table->dropForeign('temp_stocks_item_detail_id_foreign');
            $table->dropColumn('item_detail_id');
        });
        Schema::enableForeignKeyConstraints();
    }
}
