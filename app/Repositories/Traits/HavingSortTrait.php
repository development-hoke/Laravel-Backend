<?php

namespace App\Repositories\Traits;

use Illuminate\Support\Facades\DB;

/**
 * sortカラムの張替えなど。
 */
trait HavingSortTrait
{
    /**
     * @param Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    protected function resetSort($model)
    {
        $affectedRows = $this->model
            ->where('sort', '>=', $model->sort)
            ->where('id', '!=', $model->id)
            ->get();
        $this->resetModel();

        if (!$affectedRows->first()) {
            return;
        }

        $this->model->whereIn('id', $affectedRows->pluck('id'))->update(['sort' => DB::raw('sort + 1')]);
        $this->resetModel();

        DB::statement('SET @sort:=0');
        DB::statement("UPDATE {$this->model->getTable()} SET sort = (@sort := @sort + 1) ORDER BY sort");
    }
}
