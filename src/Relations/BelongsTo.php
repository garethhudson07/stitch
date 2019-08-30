<?php

namespace Stitch\Relations;

use Stitch\Queries\Joins\BelongsTo as Join;
use Stitch\Records\Relations\Collection as RecordCollection;
use Stitch\Schema\ForeignKey;

/**
 * Class Has
 * @package Stitch\Relations
 */
class BelongsTo extends Relation
{
    /**
     * @var ForeignKey|null
     */
    protected $foreignKey;

    /**
     * @param string $column
     * @return $this
     */
    public function foreignKey(string $column)
    {
        $localTable = $this->localModel->getTable();

        $this->foreignKey = $localTable->getForeignKeyFrom($localTable->getColumn($column));

        return $this;
    }

    /**
     * @return $this
     */
    public function pullKeys()
    {
        $this->foreignKey = $this->localModel->getTable()->getForeignKeyFor(
            $this->getForeignModel()->getTable()->getPrimaryKey()
        );

        return $this;
    }

    /**
     * @return mixed
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return bool
     */
    public function hasKeys()
    {
        return $this->foreignKey !== null;
    }

    /**
     * @return mixed|Join
     */
    public function join()
    {
        return new Join($this->getForeignModel(), $this->joinBuilder(), $this);
    }

    /**
     * @return mixed|RecordCollection
     */
    public function make()
    {
    }

    /**
     * @param array $attributes
     * @return BelongsTo
     */
    public function record(array $attributes = [])
    {
    }
}