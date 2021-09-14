<?php

namespace App\Pagination;

use Illuminate\Pagination\LengthAwarePaginator as BaseLengthAwarePaginator;

class LengthAwarePaginator extends BaseLengthAwarePaginator
{
    /**
     * @var array
     */
    protected $range;

    /**
     * Create a new paginator instance.
     *
     * @param mixed $items
     * @param int $total
     * @param int $perPage
     * @param int|null $currentPage
     * @param array $options (path, query, fragment, pageName, range)
     *
     * @return void
     */
    public function __construct($items, $total, $perPage, $currentPage = null, array $options = [])
    {
        parent::__construct($items, $total, $perPage, $currentPage, $options);
    }

    /**
     * レンジになる任意の要素を返す
     *
     * @return array
     */
    public function range()
    {
        return $this->range;
    }
}
