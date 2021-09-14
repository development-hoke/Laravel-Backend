<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreIdToItemDetailIndividualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_detail_individuals', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable(false)->comment('stores.id')->after('product_id');
            $table->foreign('store_id')->references('id')->on('stores')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_detail_individuals', function (Blueprint $table) {
            $table->dropForeign('item_detail_individuals_store_id_foreign');
            $table->dropColumn('store_id');
        });
    }
}
