<?php

namespace Stitch\Result;

use Stitch\Contracts\Arrayable;

/**
 * Class Record
 * @package Stitch\Result
 */
class Record implements Arrayable
{
    /**
     * @var Blueprint
     */
    protected $blueprint;

    /**
     * @var Set|Record|null
     */
    protected $parent = null;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Record constructor.
     * @param Blueprint $blueprint
     */
    public function __construct(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
    }

    /**
     * @param Set|Record|null $parent
     * @return $this
     */
    public function parent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @param $raw
     * @return $this
     */
    public function extractRelations(array $raw)
    {
        foreach ($this->blueprint->relations() as $key => $blueprint) {
            if (($this->relations[$key] ?? false) && method_exists($this->relations[$key], 'extract')) {
                $this->relations[$key]->extract($raw);
            } else {
                $this->relations[$key] = $blueprint->extract($raw, $this);
            }
        }

        return $this;
    }

    /**
     * @param array $raw
     * @return $this
     */
    public function extract(array $raw)
    {
        foreach ($this->blueprint->columns() as $column) {
            if (array_key_exists($column->alias()->assembled(), $raw)) {
                $this->data[$column->name()] = $column->getSchema()->cast(
                    $raw[$column->alias()->assembled()]
                );
            }
        }

        return $this->extractRelations($raw);
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return $this->getPrimaryKey() === null;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->data[$key];
    }

    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->data[$this->blueprint->table()->primaryKey()->name()] ?? null;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @return Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

    /**
     * @return mixed
     */
    public function hydrate()
    {
        $event = $this->blueprint->event('hydrating');
        $event->fillPayload(['record' => $this])->fire();

        if ($event->defaultPrevented()) {
            return $this;
        }

        $activeRecord = $this->blueprint->activeRecord($this->data)->markAsPersisted();

        foreach ($this->relations as $name => $relation) {
            $activeRecord->setRelation($name, is_null($relation) ? null : $relation->hydrate());
        }

        $this->blueprint->event('hydrated')->fillPayload(['record' => $this, 'activeRecord' => $activeRecord])->fire();

        return $activeRecord;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge($this->data, array_map(function ($relation) {
            return is_null($relation) ? null : $relation->toArray();
        }, $this->relations));
    }

    /**
     * The record will remove itself from its parent (either from a collection of other records or as a related record)
     *
     * @return bool
     */
    public function remove(): bool
    {
        if ($this->parent instanceof Set) {
            return $this->parent->remove($this->getPrimaryKey());
        }

        if ($this->parent instanceof Record) {
            return $this->parent->removeRelatedRecord($this);
        }
    }

    /**
     * @param Record $record
     * @return bool
     */
    public function removeRelatedRecord(Record $record): bool
    {
        foreach ($this->relations as $key => $relation) {
            if ($relation === $record) {
                unset($this->relations[$key]);

                return true;
            }
        }

        return false;
    }
}
