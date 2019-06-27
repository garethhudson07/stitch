<?php

namespace Stitch;

use Stitch\Contracts\Arrayable;
use Stitch\DBAL\Builders\Record as RecordBuilder;
use Stitch\DBAL\Dispatcher;
use Stitch\Schema\Column;

/**
 * Class Record
 * @package Stitch
 */
class Record implements Arrayable
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var bool
     */
    protected $exists;

    /**
     * Record constructor.
     * @param Model $model
     * @param array $attributes
     * @param bool $exists
     */
    public function __construct(Model $model, array $attributes = [], bool $exists = false)
    {
        $this->model = $model;
        $this->attributes = $attributes;
        $this->exists = $exists;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setAttribute(string $key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setRelation(string $key, $value)
    {
        $this->relations[$key] = $value;

        return $this;
    }

    /**
     * @param $attribute
     * @return mixed|null
     */
    public function __get($attribute)
    {
        return $this->getAttribute($attribute) ?: $this->getRelation($attribute);
    }

    /**
     * @param $attribute
     * @param $value
     */
    public function __set($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getRelation(string $key)
    {
        return $this->relations[$key] ?? null;
    }

    /**
     * @return Record
     */
    public function save()
    {
        return $this->exists ? $this->update() : $this->insert();
    }

    /**
     * @return $this
     */
    protected function update()
    {
        $builder = $this->builder()
            ->primaryKey($this->model->getTable()->getPrimaryKey()->getName());

        Dispatcher::update($builder);

        return $this;
    }

    /**
     * @return RecordBuilder
     */
    protected function builder()
    {
        $builder = new RecordBuilder($this->model->getTable()->getName());

        foreach ($this->persistableAttributes() as $key => $value) {
            $builder->attribute($key, $value);
        }

        return $builder;
    }

    /**
     * @return array
     */
    protected function persistableAttributes()
    {
        $attributes = [];

        foreach ($this->model->getTable()->getColumns() as $column) {
            /** @var Column $column */
            $name = $column->getName();

            if (array_key_exists($name, $this->attributes)) {
                $attributes[$name] = $this->attributes[$name];
            }
        }

        return $attributes;
    }

    /**
     * @return $this
     */
    protected function insert()
    {
        Dispatcher::insert($this->builder());

        $this->exists = true;

        return $this;
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge($this->attributes, array_map(function ($relation) {
            /** @var Arrayable $relation */
            return $relation->toArray();
        }, $this->relations));
    }
}