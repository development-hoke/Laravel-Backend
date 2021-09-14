<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrganizationIdToItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('system');
            $table->bigInteger('organization_id')->unsigned()->nullable(false)->index()->comment('organizations.id')->after('season_number');
            $table->foreign('organization_id')->references('id')->on('organizations')->onUpdate('cascade')->onDelete('restrict');
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
            $table->dropForeign('items_organization_id_foreign');
            $table->dropColumn('organization_id');
            // $table->unsignedTinyInteger('system')->nullable()->default(\App\Enums\Common\System::Ec)->comment('組織')->after('season_number');
            $table->unsignedTinyInteger('system')->nullable()->default(1)->comment('組織')->after('season_number');
        });
    }
}
