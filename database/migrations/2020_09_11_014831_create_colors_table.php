<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colors', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->nullable(false)->unique()->comment('色番');
            $table->string('name', 100)->nullable(false)->comment('色名称');
            $table->string('color_panel', 8)->nullable(true)->comment('カラーパネル');
            $table->string('display_name', 100)->nullable(true)->comment('表示名番名');
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
        Schema::dropIfExists('colors');
    }
}
