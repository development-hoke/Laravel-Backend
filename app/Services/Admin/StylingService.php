<?php

namespace App\Services\Admin;

use App\Entities\StaffStart\Styling;
use App\HttpCommunication\StaffStart\Paginator;
use App\HttpCommunication\StaffStart\StaffStartInterface as StaffStart;

class StylingService extends Service implements StylingServiceInterface
{
    /**
     * @var StaffStart
     */
    private $staffStart;

    /**
     * @param StaffStart $staffStart
     */
    public function __construct(StaffStart $staffStart)
    {
        $this->staffStart = $staffStart;
    }

    /**
     * @param array $params
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(array $params)
    {
        $paginator = new Paginator($params);

        $params = $paginator->transform($params);

        $params = $this->staffStart->mapParams($params);

        $data = $this->staffStart->fetchCoordinates($params)->getBody();

        $stylings = Styling::collection($data['item']);

        return $paginator->createClientPaginator($stylings, $data['total']);
    }
}
