<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlineCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_categories', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->nullable(false)->comment('番号');
            $table->bigInteger('parent_id')->unsigned()->nullable(false)->index()->comment('親ID');
            $table->string('name', 255)->nullable(false)->comment('オンライン分類名');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->primary('id');
            $table->foreign('parent_id')->references('id')->on('online_categories')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('online_categories');
    }
}
