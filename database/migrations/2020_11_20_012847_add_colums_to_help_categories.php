<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

class AddColumsToHelpCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_categories', function (Blueprint $table) {
            $table->bigInteger('root_id')->unsigned()->nullable(false)->index()->comment('ルートカテゴリID（自身がルートの場合は自分自身のID）')->after('parent_id');
            $table->tinyInteger('level')->unsigned()->nullable(false)->comment('ルートからカウントしたカテゴリの階層')->after('root_id');
            $table->foreign('root_id')->references('id')->on('help_categories')->onUpdate('cascade')->onDelete('restrict');
            $table->unsignedInteger(NestedSet::LFT)->default(0)->after('name');
            $table->unsignedInteger(NestedSet::RGT)->default(0)->after(NestedSet::LFT);
            $table->index(NestedSet::getDefaultColumns());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('help_categories', function (Blueprint $table) {
            $table->dropForeign('help_categories_root_id_foreign');
            $table->dropColumn('root_id');
            $table->dropColumn('level');
            $table->dropIndex(NestedSet::getDefaultColumns());
            $table->dropColumn(NestedSet::LFT);
            $table->dropColumn(NestedSet::RGT);
        });
    }
}
