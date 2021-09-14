<?php

namespace App\Entities;

use App\Exceptions\FatalException;
use App\Utils\Arr;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

abstract class Entity implements ArrayAccess, Arrayable
{
    /**
     * データのプレフィックスを指定する。
     * ネストされたデータから取り出す。
     *
     * @var string
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * toArray($present = true)のときに使用する
     *
     * @var array
     */
    protected $visible = [];

    /**
     * castの設定
     * 配列の添字にプロパティ名を、値に\App\Utils\Castクラスのメソッドを指定する。
     *
     * @see src/server/app/Utils/Cast.php
     *
     * @var array
     */
    protected $cast = [];

    /**
     * 関連エンティティの定義
     * 値はEntity::class または [Entity::class] (collectionメソッドの定義が必須)の形で定義する
     *
     * @var array
     */
    protected $relatedEntities = [];

    /**
     * attributesに変換する際に使用する関数
     *
     * @var array
     */
    protected $attributeConverters = [];

    /**
     * attributesに変換する際に使用するデフォルトの関数
     *
     * NOTE: デフォルトでスネークケースへの変換を最初に行うようにする。
     *       同様に、プロパティ名の変換は他の処理よりも先にくるように注意する。
     *
     * @var array
     */
    protected $defaultAttributeConverters = [
        'snakeAttributeKey',
        'trimStringAttribute',
        'castAttribute',
        'convertRelatedEntity',
    ];

    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->attributes = $data instanceof static
            ? $data->getAttribues()
            : $this->toAttributes($data);
    }

    /**
     * @param mixed $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        if (isset($this->relatedEntities[$name]) && is_array($this->relatedEntities[$name])) {
            $entity = current($this->relatedEntities[$name]);

            if (!method_exists($entity, 'collection')) {
                throw new FatalException(__('error.entity_doesnot_support_collection', ['name' => $entity]));
            }

            return call_user_func([$entity, 'collection'], []);
        }

        return null;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @return array
     */
    public function getAttribues()
    {
        return $this->attributes;
    }

    /**
     * @param array $data
     *
     * @return \App\Entities\Collection
     */
    public static function collection($data = [])
    {
        return new Collection(array_map(function ($item) {
            return new static($item instanceof Arrayable ? $item->toArray() : $item);
        }, $data));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $attributes = $this->getAttribues();
        $data = [];

        foreach ($attributes as $key => $value) {
            $data[$key] = $value instanceof Arrayable ? $value->toArray() : $value;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function toVisibleArray()
    {
        $attributes = $this->getAttribues();
        $visible = Arr::dict($this->getVisible());
        $data = [];

        foreach ($attributes as $key => $value) {
            if (!isset($visible[$key])) {
                continue;
            }

            if (method_exists($value, 'toVisibleArray')) {
                $data[$key] = $value->toVisibleArray();
            } elseif ($value instanceof Arrayable) {
                $data[$key] = $value->toArray();
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * 元データを取り込むための変換処理
     *
     * @param array $data
     *
     * @return array
     */
    protected function toAttributes($data)
    {
        $attributes = [];

        if (!empty($data) && isset($this->prefix)) {
            $data = $data[$this->prefix];
        }

        foreach ($data as $key => $value) {
            [$key, $value] = $this->convertAttribute($key, $value);
            $attributes[$key] = $value;
        }

        return $attributes;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return array
     */
    protected function convertAttribute($key, $value)
    {
        $converters = array_merge($this->defaultAttributeConverters, $this->attributeConverters);

        foreach ($converters as $converter) {
            if (method_exists($this, $converter)) {
                [$key, $value] = $this->{$converter}($key, $value);
                continue;
            }

            throw new FatalException(__('error.entity_not_supported_attribute_converter', ['name' => $converter]));
        }

        return [$key, $value];
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return array
     */
    protected function snakeAttributeKey($key, $value)
    {
        return [Str::snake($key), $value];
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return array
     */
    protected function trimStringAttribute($key, $value)
    {
        return [$key, is_string($value) ? trim($value) : $value];
    }

    /**
     * $cast配列の設定に従って、値のキャストをする
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return array
     */
    protected function castAttribute($key, $value)
    {
        if (!isset($this->cast[$key])) {
            return [$key, $value];
        }

        $cast = $this->cast[$key];

        if (!method_exists(\App\Utils\Cast::class, $cast)) {
            throw new FatalException(__('error.entity_cast_not_available', ['cast' => $cast]));
        }

        return [$key, call_user_func([\App\Utils\Cast::class, $cast], $value)];
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return Entity|Collection
     */
    protected function convertRelatedEntity($key, $value)
    {
        if (!$value || !isset($this->relatedEntities[$key])) {
            return [$key, $value];
        }

        $entity = $this->relatedEntities[$key];

        $isCollection = is_array($entity);

        if ($isCollection) {
            $entity = current($entity);
        }

        if (!class_exists($entity)) {
            throw new FatalException(__('error.entity_undefined', ['name' => $entity]));
        }

        if ($isCollection && !method_exists($entity, 'collection')) {
            throw new FatalException(__('error.entity_doesnot_support_collection', ['name' => $entity]));
        }

        return [
            $key,
            $isCollection
                ? call_user_func([$entity, 'collection'], $value)
                : new $entity($value),
        ];
    }

    public function offsetSet($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function offsetExists($key)
    {
        return isset($this->attributes[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->attributes[$key]);
    }

    public function offsetGet($key)
    {
        return $this->attributes[$key] ?? null;
    }
}
