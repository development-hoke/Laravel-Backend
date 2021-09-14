<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTablesRelatedToStylings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('styling_items', function (Blueprint $table) {
            $table->dropForeign('styling_items_styling_id_foreign');
            $table->dropColumn('styling_id');
            $table->bigInteger('coordinate_id')->unsigned()->nullable(false)->index()->comment('スタッフスタートAPIのcid')->after('id');
            $table->unique(['coordinate_id', 'item_id']);
        });
        Schema::table('items_used_same_stylings', function (Blueprint $table) {
            $table->dropForeign('items_used_same_stylings_styling_id_foreign');
            $table->dropColumn('styling_id');
            $table->unique(['item_id', 'used_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('styling_items', function (Blueprint $table) {
            $table->bigInteger('styling_id')->unsigned()->nullable(false)->comment('styling.id')->after('id');
            $table->foreign('styling_id')->references('id')->on('stylings')->onUpdate('cascade')->onDelete('restrict');
            $table->dropUnique(['coordinate_id', 'item_id']);
            $table->dropColumn('coordinate_id');
        });
        Schema::table('items_used_same_stylings', function (Blueprint $table) {
            $table->bigInteger('styling_id')->unsigned()->nullable(false)->index()->comment('stylings.id')->after('item_id');
            $table->foreign('styling_id')->references('id')->on('stylings')->onUpdate('cascade')->onDelete('restrict');
        });
    }
}
