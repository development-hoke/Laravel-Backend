<?php

namespace App\Services\Admin;

use App\Exceptions\CsvValidationException;
use App\Exceptions\FatalException;
use App\Exceptions\InvalidInputException;
use App\Repositories\EventItemRepository;
use App\Repositories\EventRepository;
use App\Repositories\ItemRepository;
use App\Utils\Csv\ImportCsvInterface;
use App\Utils\Format;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EventItemService extends Service implements EventItemServiceInterface
{
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var EventItemRepository
     */
    private $eventItemRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var ImportCsvInterface
     */
    private $importCsvUtil;

    /**
     * @param EventItemRepository $eventItemRepository
     * @param ItemRepository $itemRepository
     * @param ImportCsvInterface $importCsvUtil
     */
    public function __construct(
        EventRepository $eventRepository,
        EventItemRepository $eventItemRepository,
        ItemRepository $itemRepository,
        ImportCsvInterface $importCsvUtil
    ) {
        $this->eventItemRepository = $eventItemRepository;
        $this->itemRepository = $itemRepository;
        $this->importCsvUtil = $importCsvUtil;
        $this->eventRepository = $eventRepository;
    }

    /**
     * CSVの保存を実行する。
     * エラーメッセージの配列と保存済みのデータの配列を返す。
     *
     * @param array $params
     * @param int $eventId
     *
     * @return array
     */
    public function storeCsv(array $params, int $eventId)
    {
        $this->setupImportingCsv();

        return $this->importCsvUtil->import($params['content'], function (array $row) use ($eventId) {
            try {
                $row['discount_rate'] = Format::percentile2number($row['discount_rate']);

                $this->importCsvUtil->validate($row);

                $item = $this->itemRepository->findWhere([
                    'product_number' => $row['product_number'],
                ])->first();

                if (empty($item)) {
                    throw new CsvValidationException(error_format('error.resource_not_found', [
                        'product_number' => $row['product_number'],
                    ]));
                }

                $model = $this->eventItemRepository->updateOrCreate(
                    [
                        'event_id' => $eventId,
                        'item_id' => $item->id,
                    ],
                    [
                        'discount_rate' => $row['discount_rate'],
                    ]
                );

                return $model->toArray();
            } catch (ValidationException $e) {
                throw $e;
            } catch (CsvValidationException $e) {
                throw $e;
            } catch (Exception $e) {
                report($e);
                throw new FatalException(error_format('error.unexpected'), null, $e);
            }
        });
    }

    /**
     * @return void
     */
    private function setupImportingCsv()
    {
        $this->importCsvUtil->setHeaders([
            0 => 'product_number',
            1 => 'discount_rate',
        ]);

        $this->importCsvUtil->setValidationRules([
            'product_number' => 'required|string|max:255',
            'discount_rate' => 'required|numeric',
        ]);

        $this->importCsvUtil->setValidationAttributes([
            'product_number' => __('validation.attributes.item.product_number'),
            'discount_rate' => __('validation.attributes.event_item.discount_rate'),
        ]);
    }

    /**
     * @param int $eventId
     * @param int $limit
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate(int $eventId, int $limit)
    {
        $eventItems = $this->eventItemRepository
            ->with([
                'item.itemImages',
                'item.itemDetails',
            ])
            ->scopeQuery(function ($query) use ($eventId) {
                return $query->where('event_id', $eventId);
            })
            ->paginate($limit);

        return $eventItems;
    }

    /**
     * @param array $attributes
     * @param int $eventId
     * @param int $id
     *
     * @return \App\Models\EventItem
     */
    public function update(array $attributes, int $eventId, int $id)
    {
        if (!empty($attributes['product_number'])) {
            $item = $this->itemRepository->findWhere(['product_number' => $attributes['product_number']])->first();
            $attributes = array_merge(Arr::except($attributes, ['product_number']), ['item_id' => $item->id]);
        }

        $eventItem = $this->eventItemRepository->updateWithCondition(
            $attributes,
            $id,
            ['event_id' => $eventId]
        );

        $eventItem->load([
            'item.itemImages',
            'item.itemDetails',
        ]);

        return $eventItem;
    }

    /**
     * @param array $attributes
     * @param int $eventId
     *
     * @return * @return \App\Models\EventItem
     */
    public function store(array $attributes, int $eventId)
    {
        $item = $this->itemRepository->findWhere([
            'product_number' => $attributes['product_number'],
        ])->first();

        $event = $this->eventRepository->find($eventId);

        if ($item->price_change_period > $event->period_from) {
            throw new InvalidInputException(['product' => __('validation.event.product_date')]);
        }

        $attributes = array_merge(Arr::except($attributes, ['product_number']), [
            'item_id' => $item->id,
            'event_id' => $eventId,
        ]);

        $eventItem = $this->eventItemRepository->create($attributes);

        $eventItem->load([
            'item.itemImages',
            'item.itemDetails',
        ]);

        return $eventItem;
    }

    /**
     * @param int $eventId
     * @param int $id
     *
     * @return void
     */
    public function delete(int $eventId, int $id)
    {
        $params = ['event_id' => $eventId, 'id' => $id];

        $eventItem = $this->eventItemRepository->findWhere($params)->first();

        if (empty($eventItem)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', $params));
        }

        $this->eventItemRepository->deleteWhere($params);
    }
}
