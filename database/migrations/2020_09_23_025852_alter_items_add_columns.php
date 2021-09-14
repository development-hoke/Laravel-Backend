<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterItemsAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedTinyInteger('sales_status')->nullable(false)->comment('販売ステータス')->after('reserve_status');
            $table->text('size_optional_info')->nullable(false)->comment('サイズ追加情報')->after('note_staff_ok');
            $table->text('material_info')->nullable(false)->comment('素材情報')->after('size_caution');
            $table->boolean('is_manually_setting_recommendation')->nullable(false)->default(false)->comment('レコメンド商品の手動設定有無')->after('returnable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('sales_status');
            $table->dropColumn('size_optional_info');
            $table->dropColumn('material_info');
            $table->dropColumn('is_manually_setting_recommendation');
        });
    }
}
