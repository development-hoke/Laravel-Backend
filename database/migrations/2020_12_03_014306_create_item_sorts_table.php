<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemSortsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_sorts', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('store_brand')->nullable()->default(\App\Enums\Common\StoreBrand::Medoc)->comment('ストアブランド');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('items.id');
            $table->bigInteger('sort')->unsigned()->nullable(false)->default(0)->comment('優先順');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
            $table->index(['sort', 'store_brand']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_sorts');
    }
}
