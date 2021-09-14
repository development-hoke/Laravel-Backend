<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_contents', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedTinyInteger('store_brand')->nullable()->default(\App\Enums\Common\StoreBrand::Medoc)->comment('ストラブランド');
            $table->json('main_visuals')->nullable(false)->comment('メインビジュアル');
            $table->json('new_items')->nullable(false)->comment('新着商品');
            $table->json('recommend_items')->nullable(false)->comment('おすすめ商品');
            $table->string('background_color', 6)->nullable(false)->comment('特集の背景色');
            $table->json('features')->nullable(false)->comment('トップ表示の特集');
            $table->json('news')->nullable(false)->comment('トップ表示のnews');
            $table->unsignedTinyInteger('styling_sort')->nullable()->default(\App\Enums\TopContent\StylingSort::Pv)->comment('トップ表示のスタイリングのソートタイプ');
            $table->json('stylings')->nullable(true)->comment('トップ表示のスタイリング');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('top_contents');
    }
}
