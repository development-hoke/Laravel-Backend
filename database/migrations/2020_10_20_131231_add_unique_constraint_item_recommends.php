<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintItemRecommends extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_recommends', function (Blueprint $table) {
            $table->unique(['item_id', 'recommend_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_recommends', function (Blueprint $table) {
            $table->dropUnique(['item_id', 'recommend_item_id']);
        });
    }
}
