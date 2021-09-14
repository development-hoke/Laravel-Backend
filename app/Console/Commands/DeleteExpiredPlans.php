<?php

namespace App\Console\Commands;

use App\Services\Admin\TopContentServiceInterface;
use Illuminate\Console\Command;

class DeleteExpiredPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delete_expired_plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DeleteExpiredPlans';

    /**
     * @var TopContentServiceInterface
     */
    private $topContentService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TopContentServiceInterface $topContentService)
    {
        parent::__construct();
        $this->topContentService = $topContentService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('公開期限切れの特集を削除開始');
        $deleteCountFeature = $this->topContentService->deleteExpiredFeatures();
        $this->info('公開期限切れの特集を削除しました。 削除件数: ' . $deleteCountFeature);
        $this->info('公開期限切れのNEWSを削除開始');
        $deleteCountNews = $this->topContentService->deleteExpiredNews();
        $this->info('公開期限切れのNEWSを削除しました。 削除件数: ' . $deleteCountNews);
    }
}
