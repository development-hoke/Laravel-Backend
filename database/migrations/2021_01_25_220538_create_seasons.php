<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('season_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('code')->nullable(false)->comment('コード');
            $table->string('name', 255)->nullable(false)->comment('名称');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();
        });
        Schema::create('seasons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('season_group_id')->unsigned()->nullable(false)->index()->comment('season_groups.id');
            $table->integer('code')->nullable(false)->comment('コード');
            $table->string('name', 255)->nullable(false)->comment('名称');
            $table->string('sign', 1)->nullable(false)->comment('記号');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();
            $table->foreign('season_group_id')->references('id')->on('season_groups')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('seasons');
        Schema::dropIfExists('season_groups');
        Schema::enableForeignKeyConstraints();
    }
}
