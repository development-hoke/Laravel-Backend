<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Ymdy\KeieiInterface as KeieiHttpCommunication;
use App\Repositories\DivisionRepository;
use App\Repositories\OrganizationRepository;

class DivisionMaster extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:division_master';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '経営基幹と連携し事業部・大事業部マスタの同期をする';

    /**
     * @var KeieiHttpCommunication
     */
    private $keieiHttpCommunication;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var DivisionRepository
     */
    private $divisionRepository;

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
        OrganizationRepository $organizationRepository,
        DivisionRepository $divisionRepository
    ) {
        $this->sendStart();
        $this->keieiHttpCommunication = $keieiHttpCommunication;
        $this->organizationRepository = $organizationRepository;
        $this->divisionRepository = $divisionRepository;

        try {
            $this->info('事業部マスタの同期を開始します。');

            $this->synchronize();

            $this->info('事業部マスタの同期が完了しました。');
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
        $divisionGroupIds = $divisionIds = [];
        $response = $this->keieiHttpCommunication->fetchDivisionGroups()->getBody();

        foreach ($response['division_groups'] as $data) {
            $this->organizationRepository->updateOrCreate(
                ['id' => $data['id']],
                ['name' => $data['name']],
            );
            $divisionGroupIds[] = $data['id'];
        }

        $response = $this->keieiHttpCommunication->fetchDivisions()->getBody();

        foreach ($response['divisions'] as $data) {
            $this->divisionRepository->updateOrCreate(
                ['id' => $data['id']],
                [
                    'organization_id' => $data['group_id'],
                    'name' => $data['name'],
                    'brand_name' => $data['name'],
                    'brand_code' => $data['code'],
                    'sign' => $data['mark'],
                ],
            );
            $divisionIds[] = $data['id'];
            ++$this->success;
        }

        // todo データ削除
        // if ($divisionGroupIds) {

        // }
        // if ($divisionIds) {

        // }
    }
}
