<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Ymdy\KeieiInterface as KeieiHttpCommunication;
use App\Repositories\DepartmentGroupRepository;
use App\Repositories\DepartmentRepository;

class DepartmentMaster extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:department_master';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '経営基幹と連携し部門マスタの同期をする';

    /**
     * @var KeieiHttpCommunication
     */
    private $keieiHttpCommunication;

    /**
     * @var DepartmentGroupRepository
     */
    private $departmentGroupRepository;

    /**
     * @var DepartmentRepository
     */
    private $departmentRepository;

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
        DepartmentGroupRepository $departmentGroupRepository,
        DepartmentRepository $departmentRepository
    ) {
        $this->sendStart();
        $this->keieiHttpCommunication = $keieiHttpCommunication;
        $this->departmentGroupRepository = $departmentGroupRepository;
        $this->departmentRepository = $departmentRepository;

        try {
            $this->info('部門マスタの同期を開始します。');

            $this->synchronize();

            $this->info('部門マスタの同期が完了しました。');
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
        $departmentGroupId = $departmentId = [];
        $response = $this->keieiHttpCommunication->fetchSectionGroups()->getBody();

        foreach ($response['section_groups'] as $data) {
            $this->departmentGroupRepository->updateOrCreate(
                ['id' => $data['id']],
                [
                    'name' => $data['name'],
                ],
            );
            $departmentGroupId[] = $data['id'];
        }
        $response = $this->keieiHttpCommunication->fetchSections()->getBody();

        foreach ($response['sections'] as $data) {
            $this->departmentRepository->updateOrCreate(
                ['id' => $data['id']],
                [
                    'department_group_id' => $data['group_id'],
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'short_name' => $data['abbreviation'],
                    'sign' => $data['mark'],
                ],
            );
            $departmentId[] = $data['id'];
            ++$this->success;
        }
    }
}
