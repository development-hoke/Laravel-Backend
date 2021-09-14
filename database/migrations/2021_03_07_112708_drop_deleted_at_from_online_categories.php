<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDeletedAtFromOnlineCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('online_categories', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['root_id']);
        });
        Schema::table('online_categories', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('online_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('root_id')->references('id')->on('online_categories')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('online_categories', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['root_id']);
        });
        Schema::table('online_categories', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('online_categories')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('root_id')->references('id')->on('online_categories')->onUpdate('cascade')->onDelete('restrict');
        });
    }
}
