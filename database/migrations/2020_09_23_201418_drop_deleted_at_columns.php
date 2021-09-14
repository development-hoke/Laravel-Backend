<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDeletedAtColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_sales_types', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('item_sub_brands', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('item_images', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_sales_types', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('item_sub_brands', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
        Schema::table('item_images', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
    }
}
