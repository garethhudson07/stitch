<?php

namespace Stitch\Relations;

use Stitch\Registry;
use Stitch\DBAL\Schema\ForeignKey;
use Stitch\DBAL\Schema\Table;
use Stitch\Queries\Joins\Pivoted as PivotedJoin;
use Stitch\Records\Relations\ManyToMany as Record;
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
        $schema = $this->getPivotTable()->getForeignKeyFrom(
            $this->localModel->getTable()->getColumn($column)
        );

        $this->localPivotKey = $schema->getLocalColumn();

        $this->localKey = $this->localModel->getTable()->getColumn(
            $schema->getReferenceColumnName()
        );

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function foreignPivotKey(string $column)
    {
        $schema = $this->getPivotTable()->getForeignKeyFrom(
            $this->getForeignModel()->getTable()->getColumn($column)
        );

        $this->foreignPivotKey = $schema->getLocalColumn();

        $this->foreignKey = $this->foreignModel->getTable()->getColumn(
            $schema->getReferenceColumnName()
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function pullLocalKeys()
    {
        $schema = $this->getPivotTable()->getForeignKeyFor(
            $this->localModel->getTable()->getPrimaryKey()
        );

        $this->localPivotKey = $schema->getLocalColumn();

        $this->localKey = $this->localModel->getTable()->getColumn(
            $schema->getReferenceColumnName()
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function pullForeignKeys()
    {
        $schema = $this->getPivotTable()->getForeignKeyFor(
            $this->getForeignModel()->getTable()->getPrimaryKey()
        );

        $this->foreignPivotKey = $schema->getLocalColumn();

        $this->foreignKey = $this->foreignModel->getTable()->getColumn(
            $schema->getReferenceColumnName()
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
     * @return bool
     */
    public function hasKeys()
    {
        return ($this->localPivotKey && $this->foreignPivotKey);
    }

    /**
     * @return ManyToMany
     */
    public function pullKeys()
    {
        return $this->pullLocalKeys()->pullForeignKeys();
    }

    /**
     * @return PivotedJoin
     */
    public function join()
    {
        return new PivotedJoin(
            $this->getForeignModel(),
            $this->joinBuilder(),
            $this
        );
    }

    /**
     * @param array $attributes
     * @return Record
     */
    public function record(array $attributes = [])
    {
        return (new Record(
            $this->getForeignModel(),
            $this
        ))->fill($attributes);
    }
}
