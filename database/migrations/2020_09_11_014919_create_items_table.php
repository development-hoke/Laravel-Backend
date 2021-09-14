<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('term_id')->unsigned()->nullable(false)->index()->comment('期ID');
            $table->integer('season_number')->nullable(false)->comment('季節記号');
            // $table->unsignedTinyInteger('system')->nullable()->default(\App\Enums\Common\System::Ec)->comment('組織');
            $table->unsignedTinyInteger('system')->nullable()->default(1)->comment('組織');
            $table->bigInteger('division_id')->unsigned()->nullable(false)->index()->comment('事業部');
            $table->bigInteger('department_id')->unsigned()->nullable(false)->index()->comment('部門番号');
            $table->string('product_number', 255)->nullable(false)->unique()->comment('事部品番 ');
            $table->string('maker_product_number', 255)->nullable(false)->unique()->comment('メーカーコード品番');
            $table->unsignedTinyInteger('fashion_speed')->nullable()->default(\App\Enums\Item\FashionSpeed::Speed1)->comment('ファッション速度');
            $table->string('name', 255)->nullable(false)->comment('商品名');
            $table->unsignedInteger('retail_price')->nullable(false)->comment('上代');
            $table->datetime('price_change_period')->nullable(false)->comment('売変利率期間');
            $table->decimal('price_change_rate')->nullable(false)->comment('売変利率');
            $table->unsignedInteger('main_store_brand')->nullable()->default(\App\Enums\Common\StoreBrand::Medoc)->comment('メインストアブランド');
            $table->bigInteger('brand_id')->unsigned()->nullable(false)->index()->comment('表示ブランド名');
            $table->string('display_name', 255)->nullable(false)->comment('表示商品名');
            $table->decimal('discount_rate')->nullable(false)->comment('値引率');
            $table->boolean('is_member_discount')->nullable(false)->default(false)->comment('会員価格の有効・無効');
            $table->decimal('member_discount_rate')->nullable(true)->comment('会員値引率');
            $table->decimal('point_rate')->nullable(false)->comment('ポイント付与率');
            $table->datetime('sales_period_from')->nullable(false)->comment('販売期間(from)');
            $table->datetime('sales_period_to')->nullable(false)->comment('販売期間(to)');
            $table->text('description')->nullable(false)->comment('商品説明');
            $table->text('note_staff_ok')->nullable(true)->comment('備考');
            $table->text('size_caution')->nullable(true)->comment('サイズに関する注意書き');
            $table->text('material_caution')->nullable(true)->comment('素材に関する注意書き');
            $table->unsignedTinyInteger('status')->nullable(false)->default(false)->comment('公開ステータス');
            $table->unsignedTinyInteger('reserve_status')->nullable()->default(\App\Enums\Item\ReserveStatus::Normal)->comment('予約販売ステータス');
            $table->boolean('returnable')->nullable(false)->default(false)->comment('返品可能');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('term_id')->references('id')->on('terms')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('division_id')->references('id')->on('divisions')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('department_id')->references('id')->on('departments')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('brand_id')->references('id')->on('brands')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
