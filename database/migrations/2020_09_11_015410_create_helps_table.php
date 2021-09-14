<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('helps', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('title', 255)->nullable(false)->comment('本文');
            $table->text('body')->nullable(false)->comment('本文');
            $table->unsignedInteger('sort')->nullable(false)->default(100)->comment('表示順');
            $table->boolean('is_faq')->nullable(false)->default(false)->comment('よくある質問に表示');
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
        Schema::dropIfExists('helps');
    }
}
