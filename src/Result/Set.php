<?php

namespace Stitch\Result;

use stitch\Collection;
use Stitch\DBAL\Builders\Column;
use Stitch\Queries\Query;

/**
 * Class Set
 * @package Stitch\Result
 */
class Set extends Collection
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
     * @var Column
     */
    protected $primaryKeyAlias;

    /**
     * @var array
     */
    protected $map = [];

    /**
     * Set constructor.
     * @param $query
     * @param array $items
     */
    public function __construct($query, array $items = [])
    {
        $this->query = $query;

        $table = $query->getModel()->getTable();
        $this->columns = $table->getColumns();

        $primaryKey = $table->getPrimaryKey();
        $this->primaryKey = $primaryKey->getName();
        $this->primaryKeyAlias = "{$table->getName()}_{$this->primaryKey}";

        $this->assemble($items);
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
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
        if ($data[$this->primaryKeyAlias] !== null) {
            if ($item = $this->match($data)) {
                $item->extractRelations($data);
            } else {
                $item = new Record($this->query, $data);
                $this->items[] = $item;
                $this->map[$item->{$this->primaryKey}] = count($this->items) - 1;
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
        if ($item = $this->find($data[$this->primaryKeyAlias])) {
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
}