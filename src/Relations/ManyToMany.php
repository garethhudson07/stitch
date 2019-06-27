<?php

namespace Stitch\Relations;

use Stitch\Registry;
use Stitch\Schema\Table;
use Stitch\Queries\Relations\ManyToMany as Query;
use Closure;

class ManyToMany extends Relation
{
    protected $pivotTable;

    protected $localPivotKey;

    protected $foreignPivotKey;

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

    public function getPivotTable()
    {
        if ($this->pivotTable instanceof Table) {
            return $this->pivotTable;
        } else {
            $this->pivotTable = Registry::get($this->pivotTable);

            return $this->pivotTable;
        }
    }

    public function localPivotKey($name)
    {
        $this->localPivotKey = $this->getPivotTable()->getForeignKeyFrom($name);

        return $this;
    }

    public function foreignPivotKey($name)
    {
        $this->foreignPivotKey = $this->getPivotTable()->getForeignKeyFrom($name);

        return $this;
    }

    public function getLocalPivotKey()
    {
        return $this->localPivotKey;
    }

    public function getForeignPivotKey()
    {
        return $this->foreignPivotKey;
    }

    public function queryClass()
    {
        return Query::class;
    }

    public function hasKeys()
    {
        return ($this->localPivotKey !== null && $this->foreignPivotKey !== null);
    }

    public function pullKeys()
    {
        return $this->pullLocalPivotKey()->pullForeignPivotKey();
    }

    protected function pullLocalPivotKey()
    {
        $localTable = $this->localModel->getTable();

        $this->localPivotKey = $this->getPivotTable()->getForeignKeyFor(
            $localTable->getName(),
            $localTable->getPrimaryKey()->getName()
        );

        return $this;
    }

    protected function pullForeignPivotKey()
    {
        $foreignTable = $this->getForeignModel()->getTable();

        $this->foreignPivotKey = $this->getPivotTable()->getForeignKeyFor(
            $foreignTable->getName(),
            $foreignTable->getPrimaryKey()->getName()
        );

        return $this;
    }
}