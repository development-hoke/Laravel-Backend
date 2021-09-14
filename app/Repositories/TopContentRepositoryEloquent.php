<?php

namespace App\Repositories;

use App\Models\TopContent;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class TopContentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TopContentRepositoryEloquent extends BaseRepository implements TopContentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TopContent::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param int $planId
     *
     * @return bool
     */
    public function isPlanUsedInFeatures($planId)
    {
        $topContents = $this->model->get();
        $isUsedPlan = false;
        foreach ($topContents as $topContent) {
            $features = $topContent->features;
            foreach ($features as $feature) {
                if ($feature['plan_id'] == $planId) {
                    $isUsedPlan = true;
                    break;
                }
            }
        }

        return $isUsedPlan;
    }

    /**
     * @param int $planId
     *
     * @return bool
     */
    public function isPlanUsedInNews($planId)
    {
        $topContents = $this->model->get();
        $isUsedPlan = false;
        foreach ($topContents as $topContent) {
            $news = $topContent->news;
            foreach ($news as $new) {
                if ($new['plan_id'] == $planId) {
                    $isUsedPlan = true;
                    break;
                }
            }
        }

        return $isUsedPlan;
    }
}
