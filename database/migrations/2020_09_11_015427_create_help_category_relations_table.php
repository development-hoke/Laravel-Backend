<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpCategoryRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_category_relations', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('help_id')->unsigned()->nullable(false)->index()->comment('helps.id');
            $table->bigInteger('help_category_id')->unsigned()->nullable(false)->index()->comment('help_categories.id');
            $table->timestamps();

            $table->foreign('help_id')->references('id')->on('helps')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('help_category_id')->references('id')->on('help_categories')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('help_category_relations');
    }
}
