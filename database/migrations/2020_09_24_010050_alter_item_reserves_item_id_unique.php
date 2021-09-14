<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterItemReservesItemIdUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_reserves', function (Blueprint $table) {
            $table->unique('item_id');
            $table->dropIndex('item_reserves_item_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_reserves', function (Blueprint $table) {
            $table->index('item_id');
            $table->dropUnique('item_reserves_item_id_unique');
        });
    }
}
