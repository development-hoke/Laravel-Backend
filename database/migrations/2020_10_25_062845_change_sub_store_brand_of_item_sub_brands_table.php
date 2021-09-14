<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSubStoreBrandOfItemSubBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_sub_brands', function (Blueprint $table) {
            $table->unsignedInteger('sub_store_brand')->nullable(true)->comment('サブストアブランド')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_sub_brands', function (Blueprint $table) {
            $table->unsignedInteger('sub_store_brand')->nullable()->default(\App\Enums\Common\StoreBrand::Medoc)->comment('サブストアブランド')->change();
        });
    }
}
