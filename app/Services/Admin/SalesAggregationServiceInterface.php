<?php

namespace App\Services\Admin;

interface SalesAggregationServiceInterface
{
    /**
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function aggregateOrders(array $params);

    /**
     * @param array $params
     *
     * @return array
     */
    public function aggregateItems($params);

    /**
     * @return array
     */
    public function aggreateDailyOrder();

    /**
     * @return array
     */
    public function aggreateMonthlyOrder($by);

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getSalesOrderCsvExporter(array $params);

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getSalesOrderDetailCsvExporter(array $params);

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getSalesItemCsvExporter(array $params);
}
