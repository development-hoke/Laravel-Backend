<?php

namespace App\Criteria\Help;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FrontRelatedHelpCriteria.
 *
 * @package namespace App\Criteria\Help;
 */
class FrontRelatedHelpCriteria implements CriteriaInterface
{
    protected $helpId;

    public function __construct($helpId)
    {
        $this->helpId = $helpId;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $helpId = $this->helpId;
        $model = $model->whereIn('id', function ($subquery) use ($helpId) {
            $subquery->select('help_id')
                ->from('help_category_relations')
                ->whereIn('help_category_id', function ($subquery) use ($helpId) {
                    $subquery->select('help_category_id')
                        ->from('help_category_relations')
                        ->where('help_id', $helpId);
                });
        })
        ->where('id', '<>', $helpId)
        ->published();

        return $model;
    }
}
