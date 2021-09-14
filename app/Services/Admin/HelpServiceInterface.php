<?php

namespace App\Services\Admin;

interface HelpServiceInterface
{
    /**
     * Store
     *
     * @param array $params
     *
     * @return array
     */
    public function create(array $params);

    /**
     * Update
     *
     * @param array $request
     * @param int $helpId
     *
     * @return \App\Models\Help
     */
    public function update(array $params, int $helpId);

    /**
     * Delete
     *
     * @param array $params
     *
     * @return array
     */
    public function delete(int $helpId);
}
