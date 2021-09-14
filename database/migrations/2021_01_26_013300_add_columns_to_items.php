<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign('items_brand_id_foreign');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedInteger('retail_tax')->nullable(false)->comment('上代(税)')->after('retail_price');
            $table->decimal('tax_rate')->nullable(false)->comment('計算税率')->after('retail_tax');

            $table->bigInteger('brand_id')->unsigned()->nullable()->change();
            $table->string('display_name', 255)->nullable()->change();
            $table->decimal('discount_rate')->nullable()->change();
            $table->decimal('point_rate')->nullable()->change();
            $table->datetime('sales_period_from')->nullable()->change();
            $table->datetime('sales_period_to')->nullable()->change();
            $table->datetime('price_change_period')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->unsignedInteger('sales_status')->nullable(false)->default(\App\Enums\Item\SalesStatus::Stop)->change();
        });
        Schema::disableForeignKeyConstraints();
        Schema::table('items', function (Blueprint $table) {
            $table->foreign('brand_id')->references('id')->on('brands')->onUpdate('cascade')->onDelete('set null');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign('items_brand_id_foreign');
        });

        \DB::statement('UPDATE `items` SET `brand_id` = 1 WHERE `brand_id` IS NULL');
        \DB::statement("UPDATE `items` SET `display_name` = '' WHERE `display_name` IS NULL");
        \DB::statement('UPDATE `items` SET `discount_rate` = 0 WHERE `discount_rate` IS NULL');
        \DB::statement('UPDATE `items` SET `point_rate` = 0 WHERE `point_rate` IS NULL');
        \DB::statement("UPDATE `items` SET `sales_period_from` = '' WHERE `sales_period_from` IS NULL");
        \DB::statement("UPDATE `items` SET `sales_period_to` = '' WHERE `sales_period_to` IS NULL");
        \DB::statement("UPDATE `items` SET `price_change_period` = '' WHERE `price_change_period` IS NULL");
        \DB::statement("UPDATE `items` SET `description` = '' WHERE `description` IS NULL");

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('retail_tax');
            $table->dropColumn('tax_rate');

            $table->bigInteger('brand_id')->unsigned()->nullable(false)->change();
            $table->string('display_name', 255)->nullable(false)->change();
            $table->decimal('discount_rate')->nullable(false)->change();
            $table->decimal('point_rate')->nullable(false)->change();
            $table->datetime('sales_period_from')->nullable(false)->change();
            $table->datetime('sales_period_to')->nullable(false)->change();
            $table->datetime('price_change_period')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->unsignedInteger('sales_status')->nullable(false)->change();
        });
        Schema::table('items', function (Blueprint $table) {
            $table->foreign('brand_id')->references('id')->on('brands')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::enableForeignKeyConstraints();
    }
}
