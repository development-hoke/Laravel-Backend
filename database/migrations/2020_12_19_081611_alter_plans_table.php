<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('feature_displayed');
            $table->dropColumn('information_displayed');
            $table->boolean('status')->nullable()->default(\App\Enums\Common\Status::Unpublished)->after('title')->comment('公開ステータス');
            $table->unsignedTinyInteger('place')->nullable()->default(null)->after('thumbnail')->comment('表示場所');
            $table->unsignedTinyInteger('store_brand')->nullable()->default(\App\Enums\Common\StoreBrand::Medoc)->after('id')->comment('ストアブランド');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('feature_displayed')->nullable(false)->default(false)->comment('特集に表示するかどうか');
            $table->decimal('information_displayed')->nullable(false)->default(false)->comment('お知らせに表示するかどうか');
            $table->dropColumn('status');
            $table->dropColumn('place');
            $table->dropColumn('store_brand');
        });
    }
}
