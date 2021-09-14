<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('slug', 255)->nullable(false)->unique()->comment('スラッグ');
            $table->string('title', 255)->nullable(false)->comment('タイトル');
            $table->datetime('period_from')->nullable(true)->comment('期間(from)');
            $table->datetime('period_to')->nullable(true)->comment('期間(to)');
            $table->string('thumbnail', 255)->nullable(false)->comment('サムネイル画像URL');
            $table->boolean('feature_displayed')->nullable(false)->default(false)->comment('特集に表示するかどうか');
            $table->decimal('information_displayed')->nullable(false)->default(false)->comment('お知らせに表示するかどうか');
            $table->text('body')->nullable(false)->comment('コンテンツ');
            $table->boolean('is_item_setting')->nullable(false)->default(false)->comment('商品一覧を掲載するか');
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
        Schema::dropIfExists('plans');
    }
}
