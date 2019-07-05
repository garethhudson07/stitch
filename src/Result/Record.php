<?php

namespace Stitch\Result;

use Stitch\Contracts\Arrayable;
use Stitch\Queries\Query;
use Stitch\Queries\Relations\HasOne;
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
     * @param Query $query
     * @param array $raw
     */
    public function __construct(Query $query, array $raw)
    {
        $this->query = $query;
        $this->table = $query->getModel()->getTable();
        $this->columns = $query->getBuilder()->getSelection()->getColumns();

        $this->assemble($raw);
    }

    /**
     * @param array $raw
     */
    protected function assemble(array $raw)
    {
        $this->extract($raw)->extractRelations($raw);
    }

    /**
     * @param $raw
     * @return $this
     */
    public function extractRelations($raw)
    {
        foreach ($this->query->getRelations() as $key => $relation) {
            if (!array_key_exists($key, $this->relations)) {
                $instance = ($relation instanceof HasOne) ? new static($relation, $raw) : new Set($relation);

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

            $this->data[$name] = $this->table->getColumn($name)->cast($raw[$column->getAlias()]);
        }

        return $this;
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