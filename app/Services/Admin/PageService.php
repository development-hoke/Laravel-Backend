<?php

namespace App\Services\Admin;

use App\Repositories\PageRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class PageService extends Service implements PageServiceInterface
{
    private $pageRepository;

    /**
     * @param PageRepository $pageRepository
     */
    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param int $pageId
     *
     * @return array
     */
    public function copy(int $pageId)
    {
        try {
            DB::beginTransaction();

            $originalPage = $this->pageRepository->find($pageId);

            $count = $this->pageRepository->withTrashed()->count();

            $page = $this->pageRepository->copy(
                $pageId,
                [],
                [
                    'status' => \App\Enums\Common\Status::Unpublished,
                    'slug' => $originalPage->slug . '_' . ($count + 1),
                ]
            );

            DB::commit();

            return $page;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
