<?php

namespace Stitch\Relations;

use Stitch\Registry;
use Stitch\Schema\ForeignKey;
use Stitch\Schema\Table;
use Stitch\Queries\Relations\ManyToMany as Query;
use Closure;

/**
 * Class ManyToMany
 * @package Stitch\Relations
 */
class ManyToMany extends Relation
{
    /**
     * @var
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
     * @param mixed ...$arguments
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
     * @param $name
     * @return $this
     */
    public function localPivotKey($name)
    {
        $this->localPivotKey = $this->getPivotTable()->getForeignKeyFrom($name);

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function foreignPivotKey($name)
    {
        $this->foreignPivotKey = $this->getPivotTable()->getForeignKeyFrom($name);

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
        $localTable = $this->localModel->getTable();

        $this->localPivotKey = $this->getPivotTable()->getForeignKeyFor(
            $localTable->getName(),
            $localTable->getPrimaryKey()->getName()
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function pullForeignPivotKey()
    {
        /** @var Table $foreignTable */
        $foreignTable = $this->getForeignModel()->getTable();

        $this->foreignPivotKey = $this->getPivotTable()->getForeignKeyFor(
            $foreignTable->getName(),
            $foreignTable->getPrimaryKey()->getName()
        );

        return $this;
    }
}