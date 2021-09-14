<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedTinyInteger('store_brand')->nullable()->default(\App\Enums\Common\StoreBrand::Medoc)->comment('ストラブランド');
            $table->string('name', 255)->nullable(false)->comment('ブランド名');
            $table->string('kana', 255)->nullable(false)->comment('読み方');
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
        Schema::dropIfExists('brands');
    }
}
