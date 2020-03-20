<?php

namespace Stitch\Result;

use Stitch\Contracts\Arrayable;
use Stitch\Queries\Query;

/**
 * Class Record
 * @package Stitch\Result
 */
class Record implements Arrayable
{
    /**
     * @var Query
     */
    protected $blueprint;

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
     * @param $raw
     * @return $this
     */
    public function extractRelations(array $raw)
    {
        foreach ($this->blueprint->relations() as $key => $blueprint) {
            if (!array_key_exists($key, $this->relations)) {
                $this->relations[$key] = $blueprint->result()->extract($raw);
            } else {
                $this->relations[$key]->extract($raw);
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
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->data[$key];
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
     * @return mixed
     */
    public function hydrate()
    {
        $activeRecord = $this->blueprint->activeRecord($this->data)->markAsPersisted();

        foreach ($this->relations as $name => $relation) {
            $activeRecord->setRelation($name, $relation->hydrate());
        }

        return $activeRecord;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge($this->data, array_map(function ($relation)
        {
            return $relation->toArray();
        }, $this->relations));
    }
}
