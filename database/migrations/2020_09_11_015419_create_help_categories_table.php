<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_categories', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('parent_id')->unsigned()->nullable(false)->index()->comment('親ID');
            $table->string('name', 255)->nullable(false)->comment('ヘルプ分類名');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('help_categories')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('help_categories');
    }
}
