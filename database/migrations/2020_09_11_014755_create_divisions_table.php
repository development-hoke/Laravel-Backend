<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDivisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('name', 255)->nullable(false)->comment('事業部名称');
            $table->string('brand_name', 255)->nullable(false)->comment('ブランド名称');
            $table->string('brand_code', 255)->nullable(false)->comment('ブランドコード');
            $table->string('sign', 255)->nullable(false)->comment('正札記号');
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
        Schema::dropIfExists('divisions');
    }
}
