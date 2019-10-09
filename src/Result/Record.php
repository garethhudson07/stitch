<?php

namespace Stitch\Result;

use Stitch\Contracts\Arrayable;
use Stitch\Queries\Query;
use Stitch\Result\Blueprints\Blueprint;

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
        foreach ($this->blueprint->relations() as $key => $relation) {
            if (!array_key_exists($key, $this->relations)) {
                $this->relations[$key] = $relation->newResult()->extract($raw);
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
        foreach ($this->blueprint->columnMap() as $map) {
            if (array_key_exists($map['alias'], $raw)) {
                $this->data[$map['schema']->getName()] = $map['schema']->cast(
                    $raw[$map['alias']]
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
        $activeRecord = $this->blueprint->activeRecord()->fill($this->data);

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
