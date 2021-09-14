<?php

namespace App\Console\Commands;

use App\Domain\ItemPreviewInterface as ItemPreviewService;
use Illuminate\Console\Command;

class DeleteItemPreviewImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delete_item_preview_images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DeleteItemPreviewImages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ItemPreviewService $itemPreviewService)
    {
        try {
            $this->info('商品プレビューで保存した画像を削除します。');

            $dirs = $itemPreviewService->deleteOldItemImageDirectories();

            $this->info('商品プレビューで保存した画像の削除が完了しました。 削除件数: ' . $dirs->count());
        } catch (\Exception $e) {
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
    }
}
