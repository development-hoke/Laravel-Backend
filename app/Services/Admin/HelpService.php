<?php

namespace App\Services\Admin;

use App\Utils\Arr;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HelpService extends Service implements HelpServiceInterface
{
    /**
     * @var \App\Repositories\HelpRepository
     */
    private $helpRepository;

    /**
     * @var \App\Repositories\HelpCategoryRelationRepository
     */
    private $helpCategoryRelationRepository;

    /**
     * @param \App\Repositories\HelpRepository $helpRepository
     * @param \App\Repositories\HelpCategoryRelationRepository $helpCategoryRelationRepository
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \App\Repositories\HelpRepository $helpRepository,
        \App\Repositories\HelpCategoryRelationRepository $helpCategoryRelationRepository
    ) {
        $this->helpRepository = $helpRepository;
        $this->helpCategoryRelationRepository = $helpCategoryRelationRepository;
    }

    /**
     * Update
     *
     * @param array $params
     *
     * @return \App\Models\Help
     */
    public function create(array $params)
    {
        try {
            DB::beginTransaction();

            $help = $this->helpRepository->create($params);

            $this->helpCategoryRelationRepository->deleteAndInsertBatch(array_map(function ($id) {
                return ['help_category_id' => $id];
            }, $params['help_categories']), 'help_id', $help->id);

            DB::commit();

            return $help;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update
     *
     * @param array $request
     * @param int $helpId
     *
     * @return \App\Models\Help
     */
    public function update(array $params, int $helpId)
    {
        try {
            DB::beginTransaction();

            $this->helpRepository->update($params, $helpId);

            if (!empty($params['help_categories'])) {
                $this->helpCategoryRelationRepository->deleteAndInsertBatch(Arr::map($params['help_categories'], function ($helpCategoryId) {
                    return [
                        'help_category_id' => $helpCategoryId,
                    ];
                }), 'help_id', $helpId);
            }

            $help = $this->helpRepository->find($helpId);

            DB::commit();

            return $help;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete
     *
     * @param int $helpId
     *
     * @return \App\Models\Help
     */
    public function delete(int $helpId)
    {
        try {
            DB::beginTransaction();

            $this->helpRepository->delete($helpId);

            $this->helpCategoryRelationRepository->deleteHelp($helpId);

            DB::commit();

            return;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
