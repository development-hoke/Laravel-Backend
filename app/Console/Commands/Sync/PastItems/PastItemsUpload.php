<?php

namespace App\Console\Commands\Sync\PastItems;

use App\Console\Commands\Sync\BaseSyncCommand;
use App\Services\Admin\PastItemServiceInterface as PastItemService;

class PastItemsUpload extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:past_items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '過去の商品をアップロード';

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
    public function handle(
        PastItemService $pastItemService
    ) {
        $this->sendStart();

        try {
            $this->info('過去の商品をアップロードを開始します。');

            $content = file_get_contents(__DIR__ . '/upload.csv');

            $params = [
                'content' => $content,
                'file_name' => 'default',
            ];
            $itemBulkUpload = $pastItemService->storeItemCsv($params);

            $this->success = $itemBulkUpload['success'];

            $this->info('過去の商品をアップロードが完了しました。');
        } catch (\Exception $e) {
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
        $this->sendSuccess();
    }
}
