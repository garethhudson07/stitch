<?php

namespace Stitch\Relations;

use Stitch\Queries\Relations\Has as Query;

class Has extends Relation
{
    protected $foreignKey;

    protected function queryClass()
    {
        return Query::class;
    }

    public function foreignKey(string $column)
    {
        $this->foreignKey = $this->getForeignModel()->getTable()->getForeignKeyFrom($column);

        return $this;
    }

    public function pullKeys()
    {
        $localTable = $this->localModel->getTable();

        $this->foreignKey = $this->getForeignModel()->getTable()->getForeignKeyFor(
            $localTable->getName(),
            $localTable->getPrimaryKey()->getName()
        );

        return $this;
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function hasKeys()
    {
        return $this->foreignKey !== null;
    }
}