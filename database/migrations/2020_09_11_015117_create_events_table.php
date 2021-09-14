<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('title', 255)->nullable(false)->comment('タイトル');
            $table->datetime('period_from')->nullable(false)->comment('期間(from)');
            $table->datetime('period_to')->nullable(false)->comment('期間(to)');
            $table->bigInteger('icon_id')->unsigned()->nullable(false)->index()->comment('icons.id');
            $table->unsignedTinyInteger('target')->nullable()->default(\App\Enums\Event\Target::Employee)->comment('対象商品計上 ');
            $table->unsignedTinyInteger('sale_type')->nullable()->default(\App\Enums\Event\SaleType::Normal)->comment('セールタイプ');
            $table->unsignedTinyInteger('target_user_type')->nullable()->default(\App\Enums\Event\TargetUserType::All)->comment('対象ユーザータイプ');
            $table->unsignedTinyInteger('discout_type')->nullable()->default(\App\Enums\Event\DiscountType::Flat)->comment('割引タイプ');
            $table->decimal('discout_rate')->nullable(true)->comment('値引き率(一律)');
            $table->json('bundle_sale_info')->nullable(true)->comment('バンドル販売情報');
            $table->decimal('point_rate')->nullable(false)->comment('ポイント付与率');
            $table->boolean('published')->nullable(false)->default(false)->comment('公開・非公開');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('icon_id')->references('id')->on('icons')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
