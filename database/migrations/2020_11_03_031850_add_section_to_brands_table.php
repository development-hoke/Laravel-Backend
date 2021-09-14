<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectionToBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->unsignedTinyInteger('section')->nullable(false)->default(\App\Enums\Brand\Section::Origin)->comment('ブランド区分')->after('id');
            $table->unsignedTinyInteger('category')->nullable()->default(\App\Enums\Brand\Category::Nothing)->comment('分類')->after('kana');
            $table->dropColumn('store_brand');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->unsignedTinyInteger('store_brand')->nullable()->default(\App\Enums\Common\StoreBrand::Medoc)->comment('ストラブランド')->after('id');
            $table->dropColumn('section');
            $table->dropColumn('category');
        });
    }
}
