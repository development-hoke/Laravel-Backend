<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropDeletedAtFromBrands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('brands')->whereNotNull('deleted_at')->delete();

        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        DB::unprepared('UPDATE items SET brand_id = (SELECT id FROM brands LIMIT 1) WHERE brand_id IS null');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->softDeletes('deleted_at', 0);
        });
    }
}
