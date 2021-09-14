<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedTinyInteger('store_brand')->nullable()->default(\App\Enums\Common\StoreBrand::Medoc)->comment('ストラブランド');
            $table->json('main_visuals')->nullable(false)->comment('メインビジュアル');
            $table->json('new_items')->nullable(false)->comment('新着商品');
            $table->json('recommend_items')->nullable(false)->comment('おすすめ商品');
            $table->text('memo')->nullable(true)->comment('memo');
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
        Schema::dropIfExists('pages');
    }
}
