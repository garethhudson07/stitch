<?php

namespace Stitch\Result;

use Stitch\Contracts\Arrayable;
use Stitch\Queries\Joins\BelongsTo;
use Stitch\Queries\Query;
use Stitch\Queries\Joins\HasOne;
use Stitch\Schema\Table;

/**
 * Class Record
 * @package Stitch\Result
 */
class Record implements arrayable
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var array
     */
    protected $columns;

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
     * @param $query
     * @param array $raw
     */
    public function __construct($query, array $raw)
    {
        $this->query = $query;

        $this->table = $query->getModel()->getTable();
        $this->columns = $this->table->getColumns();

        $this->extract($raw);
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param $raw
     * @return $this
     */
    public function extractRelations($raw)
    {
        foreach ($this->query->getJoins()->all() as $key => $join) {
            if (!array_key_exists($key, $this->relations)) {
                $instance = ($join instanceof HasOne || $join instanceof BelongsTo) ? new static($join, $raw) : new Set($join);

                $this->relations[$key] = $instance->extract($raw);
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
    protected function extract(array $raw)
    {
        foreach ($this->columns as $column) {
            $name = $column->getName();
            $alias = "{$this->table->getName()}_{$name}";

            if (array_key_exists($alias, $raw)) {
                $this->data[$name] = $this->table->getColumn($name)->cast($raw[$alias]);
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
