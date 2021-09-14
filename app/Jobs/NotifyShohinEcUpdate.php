<?php

namespace App\Jobs;

use App\HttpCommunication\Shohin\ItemInterface as ShohinHttpCommunication;
use App\Repositories\ItemImageRepository;
use App\Repositories\ItemOnlineCategoryRepository;
use App\Repositories\ItemOnlineTagRepository;
use App\Repositories\ItemRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyShohinEcUpdate implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ShohinHttpCommunication $shohinHttpCommunication,
        ItemRepository $itemRepository,
        ItemOnlineTagRepository $itemOnlineTagRepository,
        ItemOnlineCategoryRepository $itemOnlineCategoryRepository,
        ItemImageRepository $itemImageRepository
    ) {
        $itemId = $this->id;
        $item = $itemRepository->find($itemId);

        $onlineTags = $itemOnlineTagRepository->where('item_id', $itemId)->get()->toArray();
        $onlineCategories = $itemOnlineCategoryRepository->where('item_id', $itemId)->get()->toArray();

        $itemImages = $itemImageRepository->where('item_id', $itemId)->with('color')->get()->toArray();

        $params = [
            'item_master_id' => $item->id,
            'display_name' => $item->display_name,
            'description' => $item->description,
            'online_tags' => $onlineTags,
            'online_categories' => $onlineCategories,
            'images' => $itemImages,
        ];

        $shohinHttpCommunication->ecUpdate($params);
    }
}
