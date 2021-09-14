<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForignSeasonIdToItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        \Artisan::call('db:seed --class=SeasonsTableSeeder');
        Schema::table('items', function (Blueprint $table) {
            $table->foreign('season_id')->references('id')->on('seasons')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign('items_season_id_foreign');
        });
        Schema::enableForeignKeyConstraints();
    }
}
