<?php

namespace App\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSortCriteria.
 *
 * @package namespace App\Criteria\ItemDetail;
 */
abstract class SortCriteria implements CriteriaInterface
{
    const QUERY_PARAM_DELIMITER = '-';

    /**
     * @var array
     */
    protected static $orderTypes = [
        'asc',
        'desc',
    ];

    /**
     * @var array
     */
    protected static $sortTypes = [];

    /**
     * @var array
     */
    protected static $sortOptions;

    /**
     * @return void
     */
    protected static function createSortOptions(): void
    {
        $options = [];

        foreach (static::$orderTypes as $orderType) {
            foreach (static::$sortTypes as $sortType) {
                $options[] = static::buildQueryParam($sortType, $orderType);
            }
        }

        static::$sortOptions = $options;
    }

    /**
     * クエリパラメータの文字列からパラメータを抽出する
     *
     * @param string $queryParams
     *
     * @return array
     */
    protected static function extractParams(string $queryParams): array
    {
        return explode(static::QUERY_PARAM_DELIMITER, $queryParams);
    }

    /**
     * ソートに使用するクエリパラメータを作成
     *
     * @param string $sortType
     * @param string $orderType
     *
     * @return string
     */
    public static function buildQueryParam(string $sortType, string $orderType): string
    {
        return implode(static::QUERY_PARAM_DELIMITER, [$sortType, $orderType]);
    }

    /**
     * ソートに使用するオプションを取得する
     *
     * @return array
     */
    public static function getSortOptions(): array
    {
        if (!isset(static::$sortOptions)) {
            static::createSortOptions();
        }

        return static::$sortOptions;
    }

    /**
     * @var array
     */
    protected $params;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    abstract public function apply($model, RepositoryInterface $repository);
}
