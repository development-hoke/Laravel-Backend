<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsOfItemBalkUploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_balk_uploads', function (Blueprint $table) {
            $table->dropColumn('upload_date');
            $table->dropColumn('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_balk_uploads', function (Blueprint $table) {
            $table->datetime('upload_date')->nullable(false)->comment('アップロード日時')->after('file_name');
            $table->softDeletes('deleted_at', 0)->after('errors');
        });
    }
}
