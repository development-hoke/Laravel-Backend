<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @SuppressWarnings(PHPMD.LongClassName)
 */
class RenameRecommendItemsToPickupsOnTopContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('top_contents', function (Blueprint $table) {
            $table->renameColumn('recommend_items', 'pickups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('top_contents', function (Blueprint $table) {
            $table->renameColumn('pickups', 'recommend_items');
        });
    }
}
