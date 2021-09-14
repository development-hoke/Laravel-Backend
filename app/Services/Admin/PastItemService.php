<?php

namespace App\Services\Admin;

use App\Exceptions\CsvValidationException;
use App\Exceptions\FatalException;
use App\Repositories\ItemDetailIdentificationRepository;
use App\Utils\Csv\ImportCsvInterface;
use App\Utils\FileUploadUtil;
use App\Utils\FileUtil;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class PastItemService extends Service implements PastItemServiceInterface
{
    /**
     * @var string
     */
    protected $delimiter = ',';

    /**
     * @var \App\Repositories\PastItemRepository
     */
    private $pastItemRepository;

    /**
     * @var \App\Repositories\ItemDetailIdentificationRepository
     */
    private $itemDetailIdentificationRepo;

    /**
     * @var \App\Domain\MemberInterface
     */
    private $memberService;

    /**
     * @var \App\Utils\Csv\ImportCsvInterface
     */
    private $importCsvUtil;

    /**
     * @param \App\Repositories\PastItemRepository $pastItemRepository
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \App\Repositories\PastItemRepository $pastItemRepository,
        \App\Domain\MemberInterface $memberService,
        ImportCsvInterface $importCsvUtil,
        ItemDetailIdentificationRepository $itemDetailIdentificationRepo
    ) {
        $this->pastItemRepository = $pastItemRepository;
        $this->itemDetailIdentificationRepo = $itemDetailIdentificationRepo;
        $this->memberService = $memberService;
        $this->importCsvUtil = $importCsvUtil;

        if (auth('admin_api')->check()) {
            $this->memberService->setStaffToken(auth('admin_api')->user()->token);
        }
    }

    /**
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(array $params)
    {
        $this->pastItemRepository->pushCriteria(
            new \App\Criteria\PastItem\AdminSearchCriteria($params)
        );

        $pastItems = $this->pastItemRepository->paginate(
            config('repository.pagination.past_item', 50)
        );

        return $pastItems;
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
                // $this->importCsvUtil->validate($row);

                $model = DB::transaction(function () use ($row) {
                    if ($this->pastItemRepository->where('old_jan_code', $row['jan_code'])->exists()) {
                        return $this->pastItemRepository->where('old_jan_code', $row['jan_code'])->first();
                    } else {
                        $row = $this->preprocessRow($row);

                        $updatedItem = $this->pastItemRepository->firstOrCreate($row);

                        return $updatedItem;
                    }
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

        $itemBulkUpload = [
            'file_name' => $params['file_name'],
            'success' => count($results['succeeded']),
            'failure' => count($results['failed']),
            'errors' => json_encode($results['failed']),
        ];

        return $itemBulkUpload;
    }

    /**
     * @return void
     */
    private function setupImportingCsv()
    {
        $this->importCsvUtil->setHeaders([
            1 => 'url_code',
            9 => 'jan_code',
            11 => 'product_number',
            12 => 'name',
            13 => 'sort',
            14 => 'price',
            15 => 'retail_price',
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

        $proccessed['old_jan_code'] = $this->checkOldJancodLeng((int) $row['jan_code']);

        $proccessed['name'] = $row['name'];

        $itemDetailId = $this->itemDetailIdentificationRepo->where('old_jan_code', $proccessed['old_jan_code'])->first();
        $proccessed['jan_code'] = isset($itemDetailId['jan_code']) ? $itemDetailId['jan_code'] : '';

        $proccessed['product_number'] = substr($proccessed['old_jan_code'], 0, 9);

        $proccessed['maker_product_number'] = explode($this->delimiter, $row['product_number'])[0];

        $proccessed['sort'] = $row['sort'];
        $proccessed['retail_price'] = $row['retail_price'];
        $proccessed['price'] = $row['price'];

        $imageUrl = isset($row['url_code']) ? $this->putNewThumbnail($row['url_code']) : '';
        $proccessed['image_url'] = $imageUrl;

        return $proccessed;
    }

    /**
     * @param string $urlCode
     * @param int $itemId
     *
     * @return string
     */
    private function putNewThumbnail(string $urlCode)
    {
        if (strlen($urlCode) == 8) {
            $urlCode = '0'.$urlCode;
        }
        $subCode = substr($urlCode, 0, 3);

        //  画像URL形式: https://ymdy.fs-storage.jp/fs2cabinet/011/011294099/011294099-m-01-dl.jpg
        $baseUrl = 'https://ymdy.fs-storage.jp/fs2cabinet/'.$subCode.'/'.$urlCode.'/'.$urlCode.'-m-01-dl.jpg';

        if ($this->getHttpResponseCode($baseUrl) != '200') {
            return '';
        }

        $content = file_get_contents($baseUrl);
        $fileName = $urlCode;
        $contentType = FileUtil::MIME_TYPE_JPG;

        $filePath = FileUploadUtil::generateNewImageFilePath(
            sprintf('%s/%s/', config('filesystems.dirs.image.past_item'), $subCode),
            $fileName,
            $contentType
        );

        $url = FileUtil::putPublicImage($filePath, $content);

        return $url;
    }

    /**
     * @param string $oldJanCode
     *
     * @return string
     */
    private function checkOldJancodLeng($oldJanCode)
    {
        if (strlen($oldJanCode) == 11) {
            $oldJanCode = '0'.$oldJanCode;
        }

        return $oldJanCode;
    }

    /**
     * @param string $url
     *
     * @return string $statusCode
     */
    private function getHttpResponseCode($url)
    {
        $headers = get_headers($url);

        return substr($headers[0], 9, 3);
    }
}
