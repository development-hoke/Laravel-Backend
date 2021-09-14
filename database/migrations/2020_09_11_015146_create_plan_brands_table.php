<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_brands', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('plan_id')->unsigned()->nullable(false)->index()->comment('plans.id');
            $table->bigInteger('brand_id')->unsigned()->nullable(false)->index()->comment('brands.id');
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('plans')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('brand_id')->references('id')->on('brands')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_brands');
    }
}
