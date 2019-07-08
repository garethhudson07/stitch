<?php

namespace Stitch\Relations;

use Stitch\Queries\Relations\Has as Query;
use Stitch\Records\Relations\Has as RecordCollection;
use Stitch\Schema\ForeignKey;

/**
 * Class Has
 * @package Stitch\Relations
 */
class Has extends Relation
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
        $this->foreignKey = $this->getForeignModel()->getTable()->getForeignKeyFrom($column);

        return $this;
    }

    /**
     * @return $this
     */
    public function pullKeys()
    {
        $localTable = $this->localModel->getTable();

        $this->foreignKey = $this->getForeignModel()->getTable()->getForeignKeyFor(
            $localTable->getName(),
            $localTable->getPrimaryKey()->getName()
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
     * @return Query
     */
    public function query()
    {
        return new Query($this->foreignModel, $this->joinBuilder(), $this);
    }

    public function make()
    {

    }
}