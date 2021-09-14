<?php

namespace App\Services\Admin;

use App\Exceptions\InvalidInputException;
use App\Repositories\EventBundleSaleRepository;
use App\Repositories\EventItemRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventUserRepository;
use App\Repositories\ItemRepository;
use App\Utils\Arr;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EventService extends Service implements EventServiceInterface
{
    private $eventRepository;
    private $eventUserRepository;
    private $eventItemRepository;
    private $eventBundleSaleRepository;
    private $itemRepository;

    /**
     * @param EventRepository $eventRepository
     * @param EventUserRepository $eventUserRepository
     * @param EventItemRepository $eventItemRepository
     */
    public function __construct(
        EventRepository $eventRepository,
        EventUserRepository $eventUserRepository,
        EventItemRepository $eventItemRepository,
        EventBundleSaleRepository $eventBundleSaleRepository,
        ItemRepository $itemRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->eventUserRepository = $eventUserRepository;
        $this->eventItemRepository = $eventItemRepository;
        $this->eventBundleSaleRepository = $eventBundleSaleRepository;
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param int $eventId
     *
     * @return array
     */
    public function copy(int $eventId)
    {
        try {
            DB::beginTransaction();

            $event = $this->eventRepository->copy(
                $eventId,
                [],
                ['published' => \App\Enums\Common\Status::Unpublished]
            );

            $eventItems = $this->eventItemRepository->copyBatch(
                ['event_id' => $eventId],
                ['event_id' => $event->id]
            );

            $eventUsers = $this->eventUserRepository->copyBatch(
                ['event_id' => $eventId],
                ['event_id' => $event->id]
            );

            DB::commit();

            return [
                $event,
                $eventItems,
                $eventUsers,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param array $params
     *
     * @return \App\Models\Event
     */
    public function create(array $params)
    {
        try {
            DB::beginTransaction();

            $event = $this->eventRepository->create(Arr::except($params, ['event_bundle_sales']));

            if (!empty($params['event_bundle_sales'])) {
                $bundleSales = [];

                foreach ($params['event_bundle_sales'] as $settings) {
                    $bundleSales[] = $this->eventBundleSaleRepository->create(array_merge(
                        $settings,
                        ['event_id' => $event->id]
                    ));
                }

                $event->setRelation('eventBundleSales', Collection::make($bundleSales));
            }

            DB::commit();

            return $event;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param array $params
     * @param int $id
     *
     * @return \App\Models\Event
     */
    public function update(array $params, int $id)
    {
        try {
            DB::beginTransaction();

            $eventItems = $this->eventItemRepository->findWhere([
                'event_id' => $id,
            ])->all();

            foreach ($eventItems as $eitem) {
                $item = $this->itemRepository->find($eitem->item_id);
                if ($params['period_from'].':00' < $item->price_change_period) {
                    throw new InvalidInputException(['product' => __('validation.event.product_date')]);
                }
            }

            $event = $this->eventRepository->update(Arr::except($params, ['event_bundle_sales']), $id);

            if (!empty($params['event_bundle_sales'])) {
                $bundleSales = $this->eventBundleSaleRepository->deleteAndInsertBatch(
                    $params['event_bundle_sales'],
                    'event_id',
                    $event->id
                );

                $event->setRelation('eventBundleSales', $bundleSales);
            }

            DB::commit();

            return $event;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $id
     *
     * @return \App\Models\Event
     */
    public function delete(int $id)
    {
        try {
            DB::beginTransaction();

            $this->eventRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
