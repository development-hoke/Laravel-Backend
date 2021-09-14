<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYmdyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->nullable(false)->unique()->comment('ID');
            $table->string('email', 255)->nullable(false)->comment('メールアドレス');
            $table->string('token', 255)->nullable(false)->comment('トークン');
            $table->string('code', 100)->nullable(false)->unique()->comment('メンバーコード');
            $table->dateTime('token_limit', 0)->nullable(true)->comment('トークン期限');
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
        Schema::dropIfExists('users');
    }
}
