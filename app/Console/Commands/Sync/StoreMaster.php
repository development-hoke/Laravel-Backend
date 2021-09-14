<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Ymdy\KeieiInterface as KeieiHttpCommunication;
use App\Repositories\StoreRepository;

class StoreMaster extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:store_master';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '経営基幹と連携し店舗マスタの同期をする';

    /**
     * @var KeieiHttpCommunication
     */
    private $keieiHttpCommunication;

    /**
     * @var StoreRepository
     */
    private $storeRepository;

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
        StoreRepository $storeRepository
    ) {
        $this->sendStart();
        $this->keieiHttpCommunication = $keieiHttpCommunication;
        $this->storeRepository = $storeRepository;

        try {
            $this->info('店舗マスタの同期を開始します。');

            $this->synchronize();

            $this->info('店舗マスタの同期が完了しました。');
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
        $storeIds = [];
        $response = $this->keieiHttpCommunication->fetchStores()->getBody();

        foreach ($response['shops'] as $data) {
            try {
                $this->storeRepository->updateOrCreate(
                    ['id' => $data['id']],
                    [
                        'code' => $data['code'],
                        'name' => $data['name'],
                        'title' => $data['title'],
                        'zip_code' => preg_replace('/\-/', '', $data['zip']),
                        'address1' => $data['address1'],
                        'address2' => $data['address2'],
                        'phone_number_1' => $data['tel1'],
                        'phone_number_2' => $data['tel2'],
                        'email' => $data['email'] ?? '',
                        'location' => ['longitude' => $data['longitude'], 'latitude' => $data['latitude']],
                        'open_time' => $data['open_time'],
                        'close_time' => $data['close_time'],
                    ],
                );
                $storeIds[] = $data['id'];
                ++$this->success;
            } catch (\Illuminate\Database\QueryException $e) {
                $this->error('SQLエラー: ' . $e->getMessage());
                ++$this->failure;
            }
        }

        // todo データ削除
        // if ($storeIds) {

        // }
    }
}
