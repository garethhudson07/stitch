<?php

namespace Stitch\Queries\Joins;

use Stitch\DBAL\Builders\Join as JoinBuilder;

/**
 * Class ManyToMany
 * @package Stitch\Queries\Relations
 */
class ManyToMany extends Join
{
    /**
     * @param Base $query
     * @return $this
     */
    public function apply()
    {
        $blueprint = $this->blueprint;
        $pivotTable = $blueprint->getPivotTable();
        $primaryPivotKey = $pivotTable->getPrimaryKey();
        $localPivotKey = $blueprint->getLocalPivotKey();
        $foreignPivotKey = $blueprint->getForeignPivotKey();

        $pivotJoinBuilder = (new JoinBuilder(
            $pivotTable->getName(),
            $primaryPivotKey ? $primaryPivotKey->getName() : null
        ))->type('LEFT')
            ->on(
                "{$pivotTable->getName()}.{$localPivotKey->getLocalColumn()->getName()}",
                '=',
                "{$localPivotKey->getReferenceTableName()}.{$localPivotKey->getReferenceColumnName()}"
            );

        $this->builder->type('LEFT')
            ->on(
                "{$foreignPivotKey->getReferenceTableName()}.{$foreignPivotKey->getReferenceColumnName()}",
                '=',
                "{$pivotTable->getName()}.{$foreignPivotKey->getLocalColumn()->getName()}"
            );

        $pivotJoinBuilder->join($this->builder);
    }
}