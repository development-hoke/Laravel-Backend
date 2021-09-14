<?php

namespace App\Services\Admin;

use App\Domain\ItemImageInterface as ItemImageService;
use App\Domain\Utils\ItemPrice;
use App\Exceptions\CsvValidationException;
use App\Exceptions\FatalException;
use App\Exceptions\InvalidInputException;
use App\Repositories\ItemBulkUploadRepository;
use App\Repositories\ItemOnlineCategoryRepository;
use App\Repositories\ItemOnlineTagRepository;
use App\Repositories\ItemRepository;
use App\Repositories\ItemSalesTypesRepository;
use App\Utils\Arr;
use App\Utils\Cast;
use App\Utils\Csv\ExportCsvInterface;
use App\Utils\Csv\ImportCsvInterface;
use App\Utils\Format;
use App\Utils\ZipInterface as ZipUtil;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ItemBulkUploadService extends Service implements ItemBulkUploadServiceInterface
{
    // 商品画像アップロード時に指定可能なファイル名のパターン
    const ACCEPTABLE_ZIP_CONTENT_FILE_NAME = '/^[0-9a-z_]+\.[a-z]+$/i';
    const ACCEPTABLE_ZIP_CONTENT_DIR_NAME = '/^[0-9a-z_]+$/i';

    /**
     * @var string
     */
    protected $delimiter = '・';

    /**
     * @var \App\Repositories\ItemRepository
     */
    private $itemRepository;

    /**
     * @var \App\Repositories\ItemSalesTypesRepository
     */
    private $itemSalesTypesRepository;

    /**
     * @var \App\Repositories\ItemOnlineTagRepository
     */
    private $itemOnlineTagRepository;

    /**
     * @var \App\Repositories\ItemOnlineCategoryRepository
     */
    private $itemOnlineCategoryRepository;

    /**
     * @var \App\Repositories\ItemBulkUploadRepository
     */
    private $itemBulkUploadRepository;

    /**
     * @var \App\Utils\Csv\ImportCsvInterface
     */
    private $importCsvUtil;

    /**
     * @var \App\Utils\Csv\ExportCsvInterface
     */
    private $exportCsvUtil;

    /**
     * @var ZipUtil
     */
    private $zipUtil;

    /**
     * @var ItemImageService
     */
    private $itemImageService;

    public function __construct(
        ItemRepository $itemRepository,
        ItemSalesTypesRepository $itemSalesTypesRepository,
        ItemOnlineTagRepository $itemOnlineTagRepository,
        ItemOnlineCategoryRepository $itemOnlineCategoryRepository,
        ItemBulkUploadRepository $itemBulkUploadRepository,
        ImportCsvInterface $importCsvUtil,
        ExportCsvInterface $exportCsvUtil,
        ZipUtil $zipUtil,
        ItemImageService $itemImageService
    ) {
        $this->itemRepository = $itemRepository;
        $this->itemSalesTypesRepository = $itemSalesTypesRepository;
        $this->itemOnlineTagRepository = $itemOnlineTagRepository;
        $this->itemOnlineCategoryRepository = $itemOnlineCategoryRepository;
        $this->itemBulkUploadRepository = $itemBulkUploadRepository;
        $this->importCsvUtil = $importCsvUtil;
        $this->exportCsvUtil = $exportCsvUtil;
        $this->zipUtil = $zipUtil;
        $this->itemImageService = $itemImageService;
    }

    /**
     * CSVの保存を実行する。
     *
     * @param array $params
     *
     * @return \App\Models\ItemBulkUpload
     */
    public function storeItemCsv(array $params)
    {
        $this->setupImportingCsv();

        $results = $this->importCsvUtil->import($params['content'], function (array $row) {
            try {
                $row = $this->preprocessRow($row);

                $this->importCsvUtil->validate($row);

                $item = $this->itemRepository->findWhere([
                    'product_number' => $row['product_number'],
                ])->first();

                if (empty($item)) {
                    throw new CsvValidationException(error_format('error.resource_not_found', [
                        'product_number' => $row['product_number'],
                    ]));
                }

                if (ItemPrice::isApplicableDiscountRate($item)) {
                    throw new CsvValidationException(__('validation.max.numeric', [
                        'attribute' => __('validation.attributes.item.discount_rate'),
                        'max' => ItemPrice::computeMaximumDiscountRate($item),
                    ]));
                }

                if (ItemPrice::isApplicableMemberDiscountRate($item)) {
                    throw new CsvValidationException(__('validation.max.numeric', [
                        'attribute' => __('validation.attributes.item.member_discount_rate'),
                        'max' => ItemPrice::computeMaximumDiscountRate($item),
                    ]));
                }

                $model = DB::transaction(function () use ($item, $row) {
                    $updatedItem = $this->itemRepository->update(Arr::except($row, ['sales_types']), $item->id);

                    if (!empty($row['sales_types'])) {
                        $updatedSalesTypes = $this->updateItemSalesTypes($row['sales_types'], $item->id);
                        $updatedItem->setRelation('salesTypes', $updatedSalesTypes);
                    }

                    if (!empty($row['online_categories'])) {
                        $updatedOnlineCategory = $this->updateItemOnlineCategories($row['online_categories'], $item->id);
                        $updatedItem->setRelation('onlineCategories', $updatedOnlineCategory);
                    }

                    if (!empty($row['online_tags'])) {
                        $updatedOnlineTag = $this->updateItemOnlineTags($row['online_tags'], $item->id);
                        $updatedItem->setRelation('onlineTags', $updatedOnlineTag);
                    }

                    return $updatedItem;
                }, 2);

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

        $itemBulkUpload = DB::transaction(function () use ($params, $results) {
            $itemBulkUpload = $this->itemBulkUploadRepository->create([
                'file_name' => $params['file_name'],
                'format' => \App\Enums\ItemBulkUpload\Format::Item,
                'upload_date' => date('Y-m-d H:i:s'),
                'status' => \App\Enums\ItemBulkUpload\Status::Complete,
                'success' => count($results['succeeded']),
                'failure' => count($results['failed']),
                'errors' => json_encode($results['failed']),
            ]);

            $this->itemBulkUploadRepository->clearOldRows();

            return $itemBulkUpload;
        }, 3);

        return $itemBulkUpload;
    }

    /**
     * @return void
     */
    private function setupImportingCsv()
    {
        $this->importCsvUtil->setHeaders([
            0 => 'product_number',
            1 => 'discount_rate',
            2 => 'is_member_discount',
            3 => 'member_discount_rate',
            4 => 'sales_status',
            5 => 'status',
            6 => 'sales_types',
            7 => 'online_categories',
            8 => 'online_tags',
        ]);

        $this->importCsvUtil->setValidationRules([
            'product_number' => 'required|string|max:255',
            'discount_rate' => 'required|numeric',
            'is_member_discount' => 'required|boolean',
            'member_discount_rate' => 'numeric|required_if:is_member_discount,1',
            'sales_status' => ['required', Rule::in(\App\Enums\Item\SalesStatus::getValues())],
            'status' => 'required|boolean',
            'sales_types.*' => 'nullable|integer|exists:sales_types,id',
            'online_categories.*' => 'nullable|integer|exists:online_categories,id',
            'online_tags.*' => 'nullable|integer|exists:online_tags,id',
        ]);

        $this->importCsvUtil->setValidationAttributes([
            'product_number' => __('validation.attributes.item.product_number'),
            'discount_rate' => __('validation.attributes.item.discount_rate'),
            'is_member_discount' => __('validation.attributes.item.is_member_discount'),
            'member_discount_rate' => __('validation.attributes.item.member_discount_rate'),
            'sales_status' => __('validation.attributes.item.sales_status'),
            'status' => __('validation.attributes.item.status'),
            'sales_types.*' => __('validation.attributes.sales_type.id'),
            'online_categories.*' => __('validation.attributes.online_category_id'),
            'online_tags.*' => __('validation.attributes.online_tag_id'),
        ]);
    }

    /**
     * @param array $row
     *
     * @return array
     */
    private function preprocessRow(array $row)
    {
        $proccessed = [];

        $status = \App\Enums\Common\Status::description2Value($row['status']);

        if ($status === null) {
            throw new CsvValidationException(__('error.invalid_value', [
                'value' => (string) $status,
                'attribute' => __('validation.attributes.item.status'),
            ]));
        }

        $proccessed['status'] = $status;

        $salesStatus = \App\Enums\Item\SalesStatus::description2Value($row['sales_status']);

        if ($salesStatus === null) {
            throw new CsvValidationException(__('error.invalid_value', [
                'value' => (string) $salesStatus,
                'attribute' => __('validation.attributes.item.sales_status'),
            ]));
        }

        $proccessed['sales_status'] = $salesStatus;

        $proccessed['discount_rate'] = Format::percentile2number($row['discount_rate']);
        $proccessed['member_discount_rate'] = Format::percentile2number($row['member_discount_rate']);
        $proccessed['is_member_discount'] = Cast::booleanLike($row['is_member_discount']);

        if (!empty($row['sales_types'])) {
            $proccessed['sales_types'] = explode($this->delimiter, $row['sales_types']);
        } else {
            $row['sales_types'] = null;
        }

        if (!empty($row['online_categories'])) {
            $proccessed['online_categories'] = explode($this->delimiter, $row['online_categories']);
        } else {
            $row['online_categories'] = null;
        }

        if (!empty($row['online_tags'])) {
            $proccessed['online_tags'] = explode($this->delimiter, $row['online_tags']);
        } else {
            $row['online_tags'] = null;
        }

        return array_merge($row, $proccessed);
    }

    /**
     * @param array $salesTypeIds
     * @param int $itemId
     *
     * @return \App\Models\ItemSalesTypes
     */
    private function updateItemSalesTypes(array $salesTypeIds, int $itemId)
    {
        $updatedSalesTypes = $this->itemSalesTypesRepository->deleteAndInsertBatch(Arr::map($salesTypeIds, function ($salesType, $index) {
            return [
                'sales_type_id' => $salesType,
                'sort' => $index + 1,
            ];
        }), 'item_id', $itemId);

        return $updatedSalesTypes;
    }

    /**
     * @param array $onlineCategoryIds
     * @param int $itemId
     *
     * @return \App\Models\OnlineCategory
     */
    private function updateItemOnlineCategories(array $onlineCategoryIds, int $itemId)
    {
        $udpatedOnlineCats = $this->itemOnlineCategoryRepository->deleteAndInsertBatch(Arr::map($onlineCategoryIds, function ($id) {
            return [
                'online_category_id' => $id,
            ];
        }), 'item_id', $itemId);

        return $udpatedOnlineCats;
    }

    /**
     * @param array $onlineTagIds
     * @param int $itemId
     *
     * @return \App\Models\OnlineTag
     */
    private function updateItemOnlineTags(array $onlineTagIds, int $itemId)
    {
        $udpatedOnlineTags = $this->itemOnlineTagRepository->deleteAndInsertBatch(Arr::map($onlineTagIds, function ($id) {
            return [
                'online_tag_id' => $id,
            ];
        }), 'item_id', $itemId);

        return $udpatedOnlineTags;
    }

    /**
     * エラーCSVを出力するコールバックを取得する
     *
     * @param int $id
     *
     * @return array
     */
    public function getErrorCsvExporter(int $id)
    {
        $itemBulkUpload = $this->itemBulkUploadRepository->find($id);

        $this->exportCsvUtil->setHeaders(['errors' => __('validation.attributes.item_bulk_upload.errors')]);

        $exporter = $this->exportCsvUtil->getExporter(function (\Closure $exporter) use ($itemBulkUpload) {
            $errors = json_decode($itemBulkUpload->errors, true);

            foreach ($errors as $error) {
                $exporter(['errors' => $error]);
            }
        });

        return [$exporter, $itemBulkUpload];
    }

    /**
     * 商品情報一括登録CSVのサンプルを取得
     *
     * @return \Closure
     */
    public function getItemCsvFormatExporter()
    {
        $this->exportCsvUtil->setHeaders([
            'product_number' => __('validation.attributes.item.product_number'),
            'discount_rate' => __('validation.attributes.item.discount_rate'),
            'is_member_discount' => __('validation.attributes.item.is_member_discount'),
            'member_discount_rate' => __('validation.attributes.item.member_discount_rate'),
            'sales_status' => __('validation.attributes.item.sales_status'),
            'status' => __('validation.attributes.item.status'),
            'sales_type' => __('resource.sales_type'),
            'online_category' => __('resource.online_category'),
            'online_tag' => __('resource.online_tag'),
        ]);

        $exporter = $this->exportCsvUtil->getExporter(function (\Closure $exporter) {
            $exporter(Lang::get('file_format.csv.admin.item_bulk_upload_csv_format_item'));
        });

        return $exporter;
    }

    /**
     * 商品画像一括登録CSVのサンプルを取得
     *
     * @return \Closure
     */
    public function getItemImageCsvFormatExporter()
    {
        $this->exportCsvUtil->setHeaders([
            'file_name' => __('file_format.csv.headers.item_image.file_name'),
            'product_number' => __('file_format.csv.headers.item_image.product_number'),
            'color_id' => __('file_format.csv.headers.item_image.color_id'),
            'caption' => __('file_format.csv.headers.item_image.caption'),
            'sort' => __('file_format.csv.headers.item_image.sort'),
        ]);

        $exporter = $this->exportCsvUtil->getExporter(function (\Closure $exporter) {
            $exporter(Lang::get('file_format.csv.admin.item_bulk_upload_csv_format_item_image'));
        });

        return $exporter;
    }

    /**
     * @param \Illuminate\Http\UploadedFile $zip
     * @param array $params
     *
     * @return \App\Models\ItemBulkUpload
     */
    public function importItemImages(\Illuminate\Http\UploadedFile $zip, array $params)
    {
        $source = $zip->getPathname();

        $destination = \App\Utils\FileUtil::generateTempDir('local');

        try {
            $extractedInfo = $this->extractZip($source, $destination);
            $csvFilePath = $extractedInfo['csv_file_path'];
            $extractedDir = $extractedInfo['extracted_dir'];

            $this->setUpImportingItemImageCsv();

            $csv = \App\Utils\FileUtil::getDisk('local')->get($csvFilePath);

            $results = $this->importCsvUtil->import($csv, function (array $row) use ($extractedDir) {
                try {
                    DB::beginTransaction();

                    $this->importCsvUtil->validate($row);

                    $item = $this->itemRepository->findWhere([
                        'product_number' => $row['product_number'],
                    ])->first();

                    if (empty($item)) {
                        throw new CsvValidationException(error_format('error.resource_not_found', [
                            'product_number' => $row['product_number'],
                        ]));
                    }

                    $sorcePath = $extractedDir . '/' . $row['file_name'];
                    $image = \App\Utils\FileUtil::getDisk('local')->get($sorcePath);

                    $itemImage = $this->itemImageService->create($item, $image, $row);

                    DB::commit();

                    return $itemImage;
                } catch (\Exception $e) {
                    DB::rollBack();

                    if ($e instanceof ValidationException || $e instanceof CsvValidationException) {
                        throw $e;
                    }

                    if ($e instanceof \Illuminate\Contracts\Filesystem\FileNotFoundException) {
                        throw new CsvValidationException(__('validation.item_bulk_upload.image_not_found', [
                            'name' => $row['file_name'],
                        ]), null, $e);
                    }

                    report($e);
                    throw new FatalException(error_format('error.unexpected'), null, $e);
                }
            });

            $itemBulkUpload = DB::transaction(function () use ($zip, $results) {
                $itemBulkUpload = $this->itemBulkUploadRepository->create([
                    'file_name' => $zip->getClientOriginalName(),
                    'format' => \App\Enums\ItemBulkUpload\Format::Item,
                    'upload_date' => date('Y-m-d H:i:s'),
                    'status' => \App\Enums\ItemBulkUpload\Status::Complete,
                    'success' => count($results['succeeded']),
                    'failure' => count($results['failed']),
                    'errors' => json_encode($results['failed']),
                ]);

                $this->itemBulkUploadRepository->clearOldRows();

                return $itemBulkUpload;
            }, 3);

            return $itemBulkUpload;
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $disk = \App\Utils\FileUtil::getDisk('local');

            if ($disk->exists($destination)) {
                \App\Utils\FileUtil::getDisk('local')->deleteDirectory($destination);
            }
        }
    }

    /**
     * @param string $source
     * @param string $destination
     *
     * @return array
     */
    private function extractZip(string $source, string $destination)
    {
        $this->zipUtil->extract($source, config('filesystems.disks.local.root') . '/' . $destination);

        $disk = \App\Utils\FileUtil::getDisk('local');

        if ($directory = Arr::find($disk->directories($destination), function ($dir) {
            return !in_array(basename($dir), \App\Utils\FileUtil::getIgnoreZipContentNames());
        })) {
            if (!preg_match(self::ACCEPTABLE_ZIP_CONTENT_DIR_NAME, basename($directory))) {
                throw new CsvValidationException(__('validation.item_bulk_upload.zip_file_name_pattern'));
            }

            $destination = $directory;
        }

        $files = $disk->files($destination);

        $csvFilePath = Arr::find($files, function ($file) {
            return strtolower(substr($file, -3)) === 'csv';
        });

        if ($csvFilePath === false) {
            throw new InvalidInputException(__('validation.item_bulk_upload.csv_not_found'));
        }

        if (!preg_match(self::ACCEPTABLE_ZIP_CONTENT_FILE_NAME, basename($csvFilePath))) {
            throw new CsvValidationException(__('validation.item_bulk_upload.zip_file_name_pattern'));
        }

        $imageFilePaths = collect($files)->filter(function ($file) use ($csvFilePath) {
            return $file !== $csvFilePath;
        });

        return [
            'csv_file_path' => $csvFilePath,
            'image_file_paths' => $imageFilePaths,
            'extracted_dir' => $destination,
        ];
    }

    /**
     * @return void
     */
    private function setUpImportingItemImageCsv()
    {
        $this->importCsvUtil->setHeaders([
            0 => 'file_name',
            1 => 'product_number',
            2 => 'color_id',
            3 => 'caption',
            4 => 'sort',
        ]);

        $this->importCsvUtil->setValidationRules([
            'file_name' => sprintf('required|regex:%s|max:255', self::ACCEPTABLE_ZIP_CONTENT_FILE_NAME),
            'product_number' => 'required|string|max:255',
            'color_id' => 'required|integer',
            'caption' => 'required|string|max:255',
            'sort' => 'nullable|integer',
        ]);

        $this->importCsvUtil->setValidationAttributes([
            'file_name' => __('file_format.csv.headers.item_image.file_name'),
            'product_number' => __('file_format.csv.headers.item_image.product_number'),
            'color_id' => __('file_format.csv.headers.item_image.color_id'),
            'caption' => __('file_format.csv.headers.item_image.caption'),
            'sort' => __('file_format.csv.headers.item_image.sort'),
        ]);

        $this->importCsvUtil->setValidationMessages([
            'file_name.regex' => __('validation.item_bulk_upload.zip_file_name_pattern'),
        ]);
    }
}
