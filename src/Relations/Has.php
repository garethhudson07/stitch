<?php

namespace Stitch\Relations;

use Stitch\Queries\Relations\Has as Query;

/**
 * Class Has
 * @package Stitch\Relations
 */
class Has extends Relation
{
    /**
     * @var
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
     * @return string
     */
    protected function queryClass()
    {
        return Query::class;
    }
}