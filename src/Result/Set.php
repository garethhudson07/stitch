<?php

namespace Stitch\Result;

use Countable;
use ArrayIterator;
use IteratorAggregate;
use Stitch\Queries\Query;

class Set implements Countable, IteratorAggregate
{
    protected $query;

    protected $columns;

    protected $primaryKey;

    protected $items = [];

    protected $map = [];

    public function __construct(Query $query, array $items = [])
    {
        $this->query = $query;
        $this->columns = $query->getBuilder()->getSelection()->getColumns();

        $this->pullPrimaryKey();
        $this->assemble($items);
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
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

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

    protected function assemble($items)
    {
        foreach ($items as $item) {
            $this->extract($item);
        }
    }

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

    public function match($data)
    {
        if ($item = $this->find($data[$this->primaryKey->getAlias()])) {
            return $item;
        }

        return false;
    }

    public function find($primaryKey)
    {
        if (array_key_exists($primaryKey, $this->map)) {
            return $this->items[$this->map[$primaryKey]];
        }

        return false;
    }
}