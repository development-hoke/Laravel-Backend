<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_images', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('items.id');
            $table->unsignedTinyInteger('type')->nullable()->default(\App\Enums\ItemImage\Type::Normal)->comment('画像タイプ');
            $table->string('url', 255)->nullable(false)->comment('画像URL');
            $table->string('caption', 255)->nullable(true)->comment('写真情報');
            $table->bigInteger('color_id')->unsigned()->nullable(false)->comment('カラー');
            $table->unsignedInteger('sort')->nullable(false)->default(100)->comment('表示順');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('color_id')->references('id')->on('colors')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_images');
    }
}
