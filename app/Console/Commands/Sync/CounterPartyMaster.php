<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Ymdy\KeieiInterface as KeieiHttpCommunication;
use App\Repositories\CounterPartyRepository;

class CounterPartyMaster extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:counter_party_master';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '経営基幹と連携し取引先マスタの同期をする';

    /**
     * @var KeieiHttpCommunication
     */
    private $keieiHttpCommunication;

    /**
     * @var CounterPartyRepository
     */
    private $counterPartyRepository;

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
        CounterPartyRepository $counterPartyRepository
    ) {
        $this->sendStart();
        $this->keieiHttpCommunication = $keieiHttpCommunication;
        $this->counterPartyRepository = $counterPartyRepository;

        try {
            $this->info('取引先マスタの同期を開始します。');

            $this->synchronize();

            $this->info('取引先マスタの同期が完了しました。');
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
        $counterPartyId = [];
        $response = $this->keieiHttpCommunication->fetchCounterParties()->getBody();

        foreach ($response['counter_parties'] as $data) {
            $this->counterPartyRepository->updateOrCreate(
                ['id' => $data['id']],
                [
                    'code' => $data['code'],
                    'name' => $data['name'],
                ],
            );
            $counterPartyId[] = $data['id'];

            ++$this->success;
        }
    }
}
