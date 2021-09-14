<?php

namespace App\Console\Commands\Sync;

use App\Models\Item as ItemModel;

class EcUpdate extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ec_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'EC情報変更';

    const LIMIT = 1000;

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
    public function handle()
    {
        $this->sendStart();

        try {
            $this->info('EC情報変更します。');

            // テストするアイテムのidを入力する。
            $item = ItemModel::find(1);
            \App\Events\Model\UpdatingItem::dispatch($item);

            $this->info('EC情報変更が完了しました。');
        } catch (\Exception $e) {
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
        $this->sendSuccess();
    }
}
