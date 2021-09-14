<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Ymdy\KeieiInterface as KeieiHttpCommunication;
use App\Repositories\SeasonGroupRepository;
use App\Repositories\SeasonRepository;

class SeasonMaster extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:season_master';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '経営基幹と連携し季節マスタの同期をする';

    /**
     * @var KeieiHttpCommunication
     */
    private $keieiHttpCommunication;

    /**
     * @var SeasonGroupRepository
     */
    private $seasonGroupRepository;

    /**
     * @var SeasonRepository
     */
    private $seasonRepository;

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
        SeasonGroupRepository $seasonGroupRepository,
        SeasonRepository $seasonRepository
    ) {
        $this->sendStart();
        $this->keieiHttpCommunication = $keieiHttpCommunication;
        $this->seasonGroupRepository = $seasonGroupRepository;
        $this->seasonRepository = $seasonRepository;

        try {
            $this->info('季節マスタの同期を開始します。');

            $this->synchronize();

            $this->info('季節マスタの同期が完了しました。');
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
        $seasonGroupId = $seasonId = [];
        $response = $this->keieiHttpCommunication->fetchSeasonGroups()->getBody();

        foreach ($response['season_groups'] as $data) {
            $this->seasonGroupRepository->updateOrCreate(
                ['id' => $data['id']],
                [
                    'code' => $data['code'],
                    'name' => $data['name'],
                ],
            );
            $seasonGroupId[] = $data['id'];
        }
        $response = $this->keieiHttpCommunication->fetchSeasons()->getBody();

        foreach ($response['seasons'] as $data) {
            $this->seasonRepository->updateOrCreate(
                ['id' => $data['id']],
                [
                    'season_group_id' => $data['group_id'],
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'sign' => $data['mark'],
                ],
            );
            $seasonId[] = $data['id'];
            ++$this->success;
        }
    }
}
