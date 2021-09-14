<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemBalkUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_balk_uploads', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('file_name', 255)->nullable()->default(\App\Enums\Common\StoreBrand::Medoc)->comment('アップロードファイル名');
            $table->datetime('upload_date')->nullable(false)->comment('アップロード日時');
            $table->unsignedTinyInteger('format')->nullable()->default(\App\Enums\ItemBulkUpload\Format::Image)->comment('フォーマット');
            $table->unsignedTinyInteger('status')->nullable()->default(\App\Enums\ItemBulkUpload\Status::Processing)->comment('ステータス');
            $table->unsignedInteger('success')->nullable(false)->default(0)->comment('成功件数');
            $table->unsignedInteger('failure')->nullable(false)->default(0)->comment('失敗件数');
            $table->json('errors')->nullable(true)->comment('エラーリスト');
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
        Schema::dropIfExists('item_balk_uploads');
    }
}
