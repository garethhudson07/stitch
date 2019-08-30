<?php

namespace Stitch\Relations;

use Stitch\Registry;
use Stitch\Schema\ForeignKey;
use Stitch\Schema\Table;
use Stitch\Queries\Joins\ManyToMany as Query;
use Stitch\Queries\Joins\ManyToMany as Join;
use Closure;

/**
 * Class ManyToMany
 * @package Stitch\Relations
 */
class ManyToMany extends Relation
{
    /**
     * @var Table|null
     */
    protected $pivotTable;

    /**
     * @var ForeignKey
     */
    protected $localPivotKey;

    /**
     * @var ForeignKey
     */
    protected $foreignPivotKey;

    /**
     * @param $value
     * @return $this
     */
    public function pivot($value)
    {
        if ($value instanceof Closure) {
            $table = new Table();

            $value($table);

            $this->pivotTable = $table;
        } else {
            $this->pivotTable = $value;
        }

        return $this;
    }

    /**
     * @return mixed|Table|null
     */
    public function getPivotTable()
    {
        if ($this->pivotTable instanceof Table) {
            return $this->pivotTable;
        } else {
            $this->pivotTable = Registry::get($this->pivotTable);

            return $this->pivotTable;
        }
    }

    /**
     * @param string $column
     * @return $this
     */
    public function localPivotKey(string $column)
    {
        $this->localPivotKey = $this->getPivotTable()->getForeignKeyFrom(
            $this->localModel->getTable()->getColumn($column)
        );

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function foreignPivotKey(string $column)
    {
        $this->foreignPivotKey = $this->getPivotTable()->getForeignKeyFrom(
            $this->getForeignModel()->getTable()->getColumn($column)
        );

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocalPivotKey()
    {
        return $this->localPivotKey;
    }

    /**
     * @return mixed
     */
    public function getForeignPivotKey()
    {
        return $this->foreignPivotKey;
    }

    /**
     * @return string
     */
    public function queryClass()
    {
        return Query::class;
    }

    /**
     * @return bool
     */
    public function hasKeys()
    {
        return ($this->localPivotKey !== null && $this->foreignPivotKey !== null);
    }

    /**
     * @return ManyToMany
     */
    public function pullKeys()
    {
        return $this->pullLocalPivotKey()->pullForeignPivotKey();
    }

    /**
     * @return $this
     */
    protected function pullLocalPivotKey()
    {
        $this->localPivotKey = $this->getPivotTable()->getForeignKeyFor(
            $this->localModel->getTable()->getPrimaryKey()
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function pullForeignPivotKey()
    {
        $this->foreignPivotKey = $this->getPivotTable()->getForeignKeyFor(
            $this->getForeignModel()->getTable()->getPrimaryKey()
        );

        return $this;
    }

    /**
     * @return mixed|Join
     */
    public function join()
    {
        return new Join($this->getForeignModel(), $this->joinBuilder(), $this);
    }

    public function make()
    {
    }

    public function record(array $attributes = [])
    {
    }
}
