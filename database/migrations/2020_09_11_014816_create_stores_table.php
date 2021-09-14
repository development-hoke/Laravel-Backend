<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('code', 255)->nullable(false)->comment('店舗コード');
            $table->string('name', 255)->nullable(false)->comment('店舗名');
            $table->string('zip_code', 7)->nullable(false)->comment('郵便番号');
            $table->string('address1', 255)->nullable(false)->comment('住所1');
            $table->string('address2', 255)->nullable(false)->comment('住所2');
            $table->string('phone_number_1', 20)->nullable(false)->comment('電話番号1');
            $table->string('phone_number_2', 20)->nullable(false)->comment('電話番号2');
            $table->string('email', 255)->nullable(false)->comment('メールアドレス');
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
        Schema::dropIfExists('stores');
    }
}
