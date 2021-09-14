<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemSubBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_sub_brands', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('items.id');
            $table->unsignedInteger('sub_store_brand')->nullable()->default(\App\Enums\Common\StoreBrand::Medoc)->comment('サブストアブランド');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_sub_brands');
    }
}
