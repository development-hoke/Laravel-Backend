<?php

namespace App\Criteria\Item;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FrontSearchCriteria.
 *
 * @package namespace App\Criteria;
 *
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class FrontSearchCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    protected $params;

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
    public function apply($model, RepositoryInterface $repository)
    {
        $params = $this->params;
        $columns = ['main_store_brand', 'brand_id'];

        // キーワード検索
        if (isset($params['q'])) {
            $column = $this->findProperItemCodeCondition($params['q'], $repository);

            // フリーテキストに事部品番かメーカー品番が指定された場合の処理。
            // どちらも一意ににitemsを特定できるので、適切な商品コードが指定されていた場合、処理を終了する。
            if (!empty($column)) {
                return $model->where('items.'.$column, $params['q']);
            }

            $model = $this->attachKeywordConditions($model, $params['q']);
        }

        foreach ($columns as $column) {
            if (isset($params[$column])) {
                if (is_array($params[$column])) {
                    $model = $model->whereIn('items.'.$column, $params[$column]);
                } else {
                    $model = $model->where('items.'.$column, $params[$column]);
                }
            }
        }

        if (!empty($params['online_category_id'])) {
            $model = $model->hasOnlineCategories($params['online_category_id']);
        }

        if (!empty($params['sales_type_id'])) {
            $model = $model->whereSalesTypeId($params['sales_type_id']);
        }

        if (!empty($params['color_id'])) {
            $model = $model->whereItemDetail([
                ['item_details.color_id', $params['color_id']],
            ]);
        }

        return $model;
    }

    /**
     * 適切なアイテムコードのカラムがあればそのカラム名を返す
     *
     * @param string $itemCodeCandidate
     * @param RepositoryInterface $repository
     *
     * @return string|null
     */
    private function findProperItemCodeCondition(string $itemCodeCandidate, RepositoryInterface $repository)
    {
        $codeColumns = ['product_number', 'maker_product_number'];

        foreach ($codeColumns as $column) {
            $item = \App\Models\Item::where($column, $itemCodeCandidate)->first();

            if (!empty($item)) {
                return $column;
            }
        }

        return null;
    }

    /**
     * キーワード検索の文字列を条件として付け加える
     * TODO: キーワード検索は必要に応じてFull-Text searchに切り替える
     *
     * @param mixed $model
     * @param string $keyword
     *
     * @return mixed
     */
    private function attachKeywordConditions($model, string $keyword)
    {
        $keywords = explode(' ', mb_convert_kana($keyword, 'KVs'));

        $model = $model->where(function ($query) use ($keywords) {
            foreach ($keywords as $word) {
                $query->orWhere(function ($query) use ($word) {
                    $condition = '%' . $word . '%';

                    return $query
                        ->where('items.display_name', 'like', "%{$condition}%")
                        ->orWhere('items.description', 'like', "%{$condition}%")
                        ->orWhere('items.material_info', 'like', "%{$condition}%");
                });
            }
        });

        return $model;
    }
}
