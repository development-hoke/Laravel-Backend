<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropStylingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('stylings');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('stylings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('coordinate_id')->unsigned()->nullable(false)->unique()->comment('スタッフスタートAPIのcid');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();
        });
    }
}
