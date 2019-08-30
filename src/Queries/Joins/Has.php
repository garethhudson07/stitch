<?php

namespace Stitch\Queries\Joins;

/**
 * Class Has
 * @package Stitch\Queries\Relations
 */
class Has extends Join
{
    public function apply()
    {
        $foreignKey = $this->blueprint->getForeignKey();
        $foreignTable = $this->blueprint->getForeignModel()->getTable();

        $this->builder->type('LEFT')
            ->on(
                "{$foreignTable->getConnection()->getDatabase()}.{$foreignTable->getName()}.{$foreignKey->getLocalColumn()->getName()}",
                '=',
                "{$this->blueprint->getLocalModel()->getTable()->getConnection()->getDatabase()}.{$foreignKey->getReferenceTableName()}.{$foreignKey->getReferenceColumnName()}"
            );
    }
}