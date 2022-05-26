<?php

namespace Stitch\Records;

use Stitch\Contracts\Arrayable;
use Stitch\Model;
use Stitch\DBAL\Builders\Record as RecordBuilder;
use Stitch\DBAL\Dispatcher;
use Stitch\Records\Relations\Aggregate as RelationAggregate;

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
    protected $attributes = [];

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var bool
     */
    protected $persisted;

    /**
     * Record constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return $this
     */
    public function markAsPersisted()
    {
        $this->persisted = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function persisted()
    {
        return $this->persisted;
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
     * @return array
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getRelation(string $name)
    {
        if ($this->hasRelation($name)) {
            return $this->relations[$name];
        }

        if ($relation = $this->model->getRelation($name)) {
            if ($relation->associatesMany()) {
                $relation = (new RelationAggregate(
                    $relation
                ))->associate($this);
            } else {
                $relation = $relation->record()->associate($this);
            }

            $this->setRelation($name, $relation);

            return $relation;
        }

        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasRelation(string $name)
    {
        return array_key_exists($name, $this->relations);
    }

    /**
     * @return Record
     */
    public function save()
    {
        return $this->persisted() ? $this->update() : $this->insert();
    }

    /**
     * @return $this
     */
    protected function update()
    {
        $table = $this->model->getTable();

        $event = $this->model->makeEvent('updating');
        $event->fillPayload(['record' => $this])->fire();

        if ($event->defaultPrevented()) {
            return $this;
        }

        Dispatcher::update(
            $table->getConnection(),
            (new RecordBuilder($table))->fill($this->attributes)
        );

        $this->model->makeEvent('updated')->fillPayload(['record' => $this])->fire();

        return $this;
    }

    /**
     * @return $this
     */
    protected function insert(): self
    {
        $table = $this->model->getTable();
        $connection = $table->getConnection();
        $primaryKey = $table->getPrimaryKey();

        $event = $this->model->makeEvent('creating');
        $event->fillPayload(['record' => $this])->fire();

        if ($event->defaultPrevented()) {
            return $this;
        }

        Dispatcher::insert(
            $connection,
            (new RecordBuilder($table))->fill($this->attributes)
        );

        if ($primaryKey->incrementing()) {
            $this->attributes[$primaryKey->getName()] = $connection->lastInsertId();
        }

        $this->markAsPersisted();

        $this->model->makeEvent('created')->fillPayload(['record' => $this])->fire();

        return $this;
    }

    /**
     * @return bool|\PDOStatement
     */
    public function delete()
    {
        $table = $this->model->getTable();

        $event = $this->model->makeEvent('deleting');
        $event->fillPayload(['record' => $this])->fire();

        if ($event->defaultPrevented()) {
            return false;
        }

        $success = Dispatcher::delete(
            $table->getConnection(),
            (new RecordBuilder($table))->fill($this->attributes)
        );

        $this->model->makeEvent('deleted')->fillPayload(['record' => $this])->fire();

        return $success;
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

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
