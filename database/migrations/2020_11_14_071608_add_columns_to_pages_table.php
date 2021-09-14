<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('store_brand');
            $table->dropColumn('main_visuals');
            $table->dropColumn('new_items');
            $table->dropColumn('recommend_items');
            $table->dropColumn('memo');
            $table->string('slug', 255)->nullable(false)->after('id')->comment('スラッグ(URI)');
            $table->string('title', 255)->nullable(false)->after('slug')->comment('タイトル');
            $table->text('body')->nullable(false)->after('title')->comment('本文');
            $table->unsignedTinyInteger('status')->nullable(false)->default(false)->after('body')->comment('公開ステータス');
            $table->datetime('publish_from')->nullable(false)->comment('公開開始日')->after('status');
            $table->datetime('publish_to')->nullable(true)->comment('公開終了日')->after('publish_from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->unsignedTinyInteger('store_brand')->nullable()->default(\App\Enums\Common\StoreBrand::Medoc)->comment('ストラブランド');
            $table->json('main_visuals')->nullable(false)->comment('メインビジュアル');
            $table->json('new_items')->nullable(false)->comment('新着商品');
            $table->json('recommend_items')->nullable(false)->comment('おすすめ商品');
            $table->text('memo')->nullable(true)->comment('memo');
            $table->dropColumn('slug');
            $table->dropColumn('title');
            $table->dropColumn('body');
            $table->dropColumn('status');
            $table->dropColumn('publish_from');
            $table->dropColumn('publish_to');
        });
    }
}
