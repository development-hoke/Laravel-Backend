<?php

namespace App\Services\Admin;

interface EventItemServiceInterface
{
    /**
     * CSVの保存を実行する。
     * エラーメッセージの配列と保存済みのデータの配列を返す。
     *
     * @param array $params
     * @param int $eventId
     *
     * @return array
     */
    public function storeCsv(array $params, int $eventId);

    /**
     * @param int $eventId
     * @param int $limit
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate(int $eventId, int $limit);

    /**
     * @param array $attributes
     * @param int $eventId
     * @param int $id
     *
     * @return \App\Models\EventItem
     */
    public function update(array $attributes, int $eventId, int $id);

    /**
     * @param array $attributes
     * @param int $eventId
     *
     * @return * @return \App\Models\EventItem
     */
    public function store(array $attributes, int $eventId);

    /**
     * @param int $eventId
     * @param int $id
     *
     * @return void
     */
    public function delete(int $eventId, int $id);
}
