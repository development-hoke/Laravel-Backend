<?php

namespace App\Models\Traits;

use App\Exceptions\FatalException;

/**
 * 特定のカラムの更新と特定のタイムスタンプカラムの更新を紐付ける。
 * 基本的にはEventなどとセットで使う。
 */
trait HasOptionalTimestampsTrait
{
    /**
     * @return \App\Models\Model
     */
    public function setOptionalTimestampsIfDirty()
    {
        return $this->setOptionalTimestamps(
            \App\Models\Contracts\Timestampable::TIMESTAMPING_TYPE_DIRTY
        );
    }

    /**
     * @return \App\Models\Model
     */
    public function setOptionalTimestampsIfNotNull()
    {
        return $this->setOptionalTimestamps(
            \App\Models\Contracts\Timestampable::TIMESTAMPING_TYPE_NOT_NULL
        );
    }

    /**
     * @param int|null $type
     *
     * @return \App\Models\Model
     */
    public function setOptionalTimestamps(?string $type = null)
    {
        if (!property_exists($this, 'optionalTimestampMap')) {
            throw new FatalException(__('error.class_propery_not_defined', [
                'class' => __CLASS__,
                'propery' => 'optionalTimestampMap',
            ]));
        }

        $map = $this->optionalTimestampMap;
        $attributes = $this->getAttributes();

        foreach ($attributes as $name => $value) {
            if (!isset($map[$name])) {
                continue;
            }

            $localType = $type;

            if (strpos($map[$name], ':') !== false) {
                [$timestampColumn, $localType] = explode(':', $map[$name]);
            } else {
                $timestampColumn = $map[$name];
            }

            if (!$this->shouldUpdateOptionalTimestamp($name, $value, $localType)) {
                continue;
            }

            $this->{$timestampColumn} = $this->freshTimestamp();
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param int|null $type
     *
     * @return array
     */
    private function shouldUpdateOptionalTimestamp($name, $value, string $type = null)
    {
        switch ($type) {
            case \App\Models\Contracts\Timestampable::TIMESTAMPING_TYPE_NOT_NULL:
                return !is_null($value);

            case \App\Models\Contracts\Timestampable::TIMESTAMPING_TYPE_DIRTY:
                return $this->isDirty($name);

            case \App\Models\Contracts\Timestampable::TIMESTAMPING_TYPE_TRUE:
                return $value == true;

            default:
                if (!method_exists($this, 'shouldUpdateOptionalTimestampHandler')) {
                    throw new FatalException(__('error.class_method_not_defined', [
                        'class' => __CLASS__,
                        'method' => 'shouldUpdateOptionalTimestampHandler',
                    ]));
                }

                return $this->shouldUpdateOptionalTimestampHandler($value);
        }
    }
}
