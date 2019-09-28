<?php

namespace Stitch\Queries\Joins;

/**
 * Class Has
 * @package Stitch\Select\Relations
 */
class BelongsTo extends Join
{
    public function apply()
    {
        $foreignKey = $this->blueprint->getForeignKey();
        $foreignTable = $this->blueprint->getForeignModel()->getTable();
        $localTable = $this->blueprint->getLocalModel()->getTable();

        $this->builder->type('LEFT')
            ->on(
                "{$foreignTable->getConnection()->getDatabase()}.{$foreignKey->getReferenceTableName()}.{$foreignKey->getReferenceColumnName()}",
                '=',
                "{$localTable->getConnection()->getDatabase()}.{$localTable->getName()}.{$foreignKey->getLocalColumn()->getName()}"
            )->limit(1);
    }
}