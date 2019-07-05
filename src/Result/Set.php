<?php

namespace Stitch\Result;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Stitch\DBAL\Builders\Column;
use Stitch\Queries\Query;

/**
 * Class Set
 * @package Stitch\Result
 */
class Set implements Countable, IteratorAggregate
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var Column
     */
    protected $primaryKey;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var array
     */
    protected $map = [];

    /**
     * Set constructor.
     * @param Query $query
     * @param array $items
     */
    public function __construct(Query $query, array $items = [])
    {
        $this->query = $query;
        $this->columns = $query->getBuilder()->getSelection()->getColumns();

        $this->pullPrimaryKey();
        $this->assemble($items);
    }

    /**
     * @return $this
     */
    protected function pullPrimaryKey()
    {
        $primaryKeyName = $this->query->getModel()->getTable()->getPrimaryKey()->getName();

        foreach ($this->columns as $column) {
            if ($column->getName() === $primaryKeyName) {
                $this->primaryKey = $column;

                return $this;
            }
        }

        return $this;
    }

    /**
     * @param $items
     */
    protected function assemble($items)
    {
        foreach ($items as $item) {
            $this->extract($item);
        }
    }

    /**
     * @param $data
     * @return $this
     */
    public function extract($data)
    {
        if ($data[$this->primaryKey->getAlias()] !== null) {
            if ($item = $this->match($data)) {
                $item->extractRelations($data);
            } else {
                $item = new Record($this->query, $data);
                $this->items[] = $item;
                $this->map[$item->{$this->primaryKey->getName()}] = count($this->items) - 1;
            }
        }

        return $this;
    }

    /**
     * @param $data
     * @return bool|mixed
     */
    public function match($data)
    {
        if ($item = $this->find($data[$this->primaryKey->getAlias()])) {
            return $item;
        }

        return false;
    }

    /**
     * @param $primaryKey
     * @return bool|mixed
     */
    public function find($primaryKey)
    {
        if (array_key_exists($primaryKey, $this->map)) {
            return $this->items[$this->map[$primaryKey]];
        }

        return false;
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($item)
        {
            return $item->toArray();
        }, $this->items);
    }
}