<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('member_id')->nullable(false)->index()->comment('Member ID');
            $table->string('last_name', 255)->nullable(false)->comment('氏');
            $table->string('first_name', 255)->nullable(false)->comment('名');
            $table->string('last_name_kana', 255)->nullable(false)->comment('氏（カナ）');
            $table->string('first_name_kana', 255)->nullable(false)->comment('名（カナ）');
            $table->string('phone', 12)->nullable(false)->comment('電話番号');
            $table->string('postal', 8)->nullable(false)->comment('郵便番号');
            $table->integer('pref_id')->nullable(false)->comment('都道府県');
            $table->string('address', 255)->nullable(false)->comment('住所');
            $table->string('building', 255)->nullable(true)->comment('ビル・マンション名等');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('destinations');
    }
}
