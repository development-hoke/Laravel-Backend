<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemDetailRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_detail_records', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('item_detail_id')->unsigned()->nullable(false)->index()->comment('item_details.id');
            $table->integer('stock')->nullable(false)->comment('在庫増減数');
            $table->unsignedInteger('sort')->nullable(false)->default(100)->comment('表示順');
            $table->text('memo')->nullable(false)->comment('理由などを記載');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('item_detail_id')->references('id')->on('item_details')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_detail_records');
    }
}
