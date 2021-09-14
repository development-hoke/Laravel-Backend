<?php

namespace App\Repositories;

use App\Models\Color;
use App\Utils\Color as ColorUtil;

/**
 * Class ColorRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ColorRepositoryEloquent extends BaseRepositoryEloquent implements ColorRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Color::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * 更新
     * brightnessを自動追加する
     *
     * @throws ValidatorException
     *
     * @param array $attributes
     * @param $id
     *
     * @return mixed
     */
    public function update(array $attributes, $id)
    {
        $attributes = $this->addBrightness($attributes);

        return parent::update($attributes, $id);
    }

    /**
     * 新規作成
     * brightnessを自動追加する
     *
     * @throws ValidatorException
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes)
    {
        $attributes = $this->addBrightness($attributes);

        return parent::create($attributes);
    }

    /**
     * color_panelがあったらbrightnessを計算して追加する
     *
     * @param array $attributes
     *
     * @return array
     */
    private function addBrightness(array $attributes)
    {
        if (!isset($attributes['color_panel'])) {
            return $attributes;
        }

        $attributes['brightness'] = ColorUtil::hex2brightness($attributes['color_panel']);

        return $attributes;
    }
}
