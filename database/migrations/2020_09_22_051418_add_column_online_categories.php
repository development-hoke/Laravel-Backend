<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOnlineCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('online_categories', function (Blueprint $table) {
            $table->bigInteger('root_id')->unsigned()->nullable(false)->index()->comment('ルートカテゴリID（自身がルートの場合は自分自身のID）')->after('parent_id');
            $table->tinyInteger('level')->unsigned()->nullable(false)->comment('ルートからカウントしたカテゴリの階層')->after('root_id');
            $table->foreign('root_id')->references('id')->on('online_categories')->onUpdate('cascade')->onDelete('restrict');
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
            $table->dropForeign('online_categories_root_id_foreign');
            $table->dropColumn('root_id');
            $table->dropColumn('level');
        });
    }
}
