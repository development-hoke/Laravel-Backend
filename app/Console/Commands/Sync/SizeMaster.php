<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Ymdy\KeieiInterface as KeieiHttpCommunication;
use App\Repositories\SizeRepository;

class SizeMaster extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:size_master';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '経営基幹と連携しサイズマスタの同期をする';

    /**
     * @var KeieiHttpCommunication
     */
    private $keieiHttpCommunication;

    /**
     * @var SizeRepository
     */
    private $sizeRepository;

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
        KeieiHttpCommunication $keieiHttpCommunication,
        SizeRepository $sizeRepository
    ) {
        $this->sendStart();
        $this->keieiHttpCommunication = $keieiHttpCommunication;
        $this->sizeRepository = $sizeRepository;

        try {
            $this->info('サイズマスタの同期を開始します。');

            $this->synchronize();

            $this->info('サイズマスタの同期が完了しました。');
        } catch (\Exception $e) {
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
        $this->sendSuccess();
    }

    /**
     * @return void
     */
    private function synchronize()
    {
        $sizeIds = [];
        $response = $this->keieiHttpCommunication->fetchSizes()->getBody();

        foreach ($response['sizes'] as $data) {
            try {
                $size = $this->sizeRepository->findWhere(['id' => $data['id']])->first();

                if (empty($size)) {
                    $size = $this->sizeRepository->makeModel();
                    $size->id = $data['id'];
                }

                $size->fill([
                    'code' => $data['code'],
                    'name' => $data['name'],
                    'search_code' => $data['search_code'],
                ]);

                $size->save();

                $sizeIds[] = $data['id'];

                ++$this->success;
            } catch (\Illuminate\Database\QueryException $e) {
                $this->error('SQLエラー: ' . $e->getMessage() . "\ntrace: " . $e->getTraceAsString());
                ++$this->failure;
            }
        }

        // todo データ削除
        // if ($sizeIds) {

        // }
    }
}
