<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Ymdy\KeieiInterface as KeieiHttpCommunication;
use App\Repositories\ColorRepository;

class ColorMaster extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:color_master';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '経営基幹と連携し色マスタの同期をする';

    /**
     * @var KeieiHttpCommunication
     */
    private $keieiHttpCommunication;

    /**
     * @var ColorRepository
     */
    private $colorRepository;

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
        ColorRepository $colorRepository
    ) {
        $this->sendStart();
        $this->keieiHttpCommunication = $keieiHttpCommunication;
        $this->colorRepository = $colorRepository;

        try {
            $this->info('色マスタの同期を開始します。');

            $this->synchronize();

            $this->info('色マスタの同期が完了しました。');
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
        $response = $this->keieiHttpCommunication->fetchColors()->getBody();

        foreach ($response['colors'] as $data) {
            $color = $this->colorRepository->findWhere(['id' => $data['id']])->first();

            if (empty($color)) {
                $color = $this->colorRepository->makeModel();
                $color->id = $data['id'];
            }

            $rgbHex = strtolower($data['rgb']);

            $color->code = $data['code'];
            $color->color_panel = $rgbHex;
            $color->brightness = \App\Utils\Color::hex2brightness($rgbHex);
            $color->name = $data['name'];
            $color->display_name = $data['name'];

            $color->save();
            ++$this->success;
        }
    }
}
