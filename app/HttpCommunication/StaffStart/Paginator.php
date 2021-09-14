<?php

namespace App\HttpCommunication\StaffStart;

use Illuminate\Pagination\LengthAwarePaginator;

class Paginator
{
    /**
     * @var int
     */
    private $perPage;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->currentPage = max($params['page'] ?? 1, 1);
        $this->perPage = $params['per_page'] ?? config('http_communication.staff_start.pagination.default_per_page');
    }

    /**
     * @return array
     */
    public function createQuery()
    {
        $page = $this->currentPage;
        $perPage = $this->perPage;
        $offset = ($this->currentPage - 1) * $perPage;

        return [
            'offset' => $offset,
            'page' => $page,
        ];
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function transform(array $params)
    {
        $query = $this->createQuery();

        unset($params['per_page']);

        return array_merge($params, $query);
    }

    /**
     * @param \App\Entities\Collection $items
     * @param int $total
     *
     * @return LengthAwarePaginator
     */
    public function createClientPaginator($items, int $total)
    {
        $paginator = new LengthAwarePaginator($items, $total, $this->perPage, $this->currentPage);

        return $paginator;
    }
}
