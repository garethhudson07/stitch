<?php

namespace Stitch\Relations;

use Stitch\Model;
use Stitch\Registry;
use Stitch\DBAL\Schema\ForeignKey;
use Stitch\Queries\Joins\Pivoted as PivotedJoin;
use Stitch\Records\Relations\ManyToMany as Record;
use Closure;
use Stitch\Stitch;

/**
 * Class ManyToMany
 * @package Stitch\Relations
 */
class ManyToMany extends Relation
{
    /**
     * @var Model|null
     */
    protected $pivot;

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
        $this->pivot = $value instanceof Closure ? $value() : $value;

        return $this;
    }

    /**
     * @return mixed|Model|null
     */
    public function getPivot()
    {
        if ($this->pivot instanceof Model) {
            return $this->pivot;
        } else {
            $this->pivot = Registry::get($this->pivot);

            return $this->pivot;
        }
    }

    /**
     * @param string $column
     * @return $this
     */
    public function localPivotKey(string $column)
    {
        $schema = $this->getPivot()->getTable()->getForeignKeyFrom(
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
        $schema = $this->getPivot()->getTable()->getForeignKeyFrom(
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
        $schema = $this->getPivot()->getTable()->getForeignKeyFor(
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
        $schema = $this->getPivot()->getTable()->getForeignKeyFor(
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
