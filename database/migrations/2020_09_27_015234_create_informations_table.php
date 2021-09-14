<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 255)->nullable(false)->comment('タイトル');
            $table->text('body')->nullable(false)->comment('本文');
            $table->unsignedInteger('priority')->nullable(false)->default(100)->comment('表示順');
            $table->boolean('is_store_top')->nullable(false)->default(false)->comment('ストアトップに表示');
            $table->string('url')->nullable(false)->comment('');
            $table->unsignedTinyInteger('status')->nullable(false)->default(false)->comment('公開ステータス');
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
        Schema::dropIfExists('informations');
    }
}
