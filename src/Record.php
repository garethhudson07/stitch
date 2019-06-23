<?php

namespace Stitch;

use Stitch\Contracts\Arrayable;
use Stitch\DBAL\Builders\Record as RecordBuilder;
use Stitch\DBAL\Dispatcher;

class Record implements Arrayable
{
    protected $model;

    protected $attributes;

    protected $relations = [];

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

    public function fill(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    public function setAttribute(string $key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function setRelation(string $key, $value)
    {
        $this->relations[$key] = $value;

        return $this;
    }

    public function getRelation(string $key)
    {
        return $this->relations[$key] ?? null;
    }

    public function __get($attribute)
    {
        return $this->getAttribute($attribute) ?: $this->getRelation($attribute);
    }

    public function __set($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    public function save()
    {
        return $this->exists ? $this->update() : $this->insert();
    }

    protected function insert()
    {
        Dispatcher::insert($this->builder());

        $this->exists = true;

        return $this;
    }

    protected function update()
    {
        $builder = $this->builder()
            ->primaryKey($this->model->getTable()->getPrimaryKey()->getName());

        Dispatcher::update($builder);

        return $this;
    }

    protected function builder()
    {
        $builder = new RecordBuilder($this->model->getTable()->getName());

        foreach ($this->persistableAttributes() as $key => $value) {
            $builder->attribute($key, $value);
        }

        return $builder;
    }

    protected function persistableAttributes()
    {
        $attributes = [];

        foreach ($this->model->getTable()->getColumns() as $column) {
            $name = $column->getName();

            if (array_key_exists($name, $this->attributes)) {
                $attributes[$name] = $this->attributes[$name];
            }
        }

        return $attributes;
    }

    public function toArray(): array
    {
        return array_merge($this->attributes, array_map(function ($relation)
        {
            return $relation->toArray();
        }, $this->relations));
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function __toString()
    {
        return $this->toJson();
    }
}