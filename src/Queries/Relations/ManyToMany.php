<?php

namespace Stitch\Queries\Relations;

use Stitch\Queries\Query;
use Stitch\DBAL\Builders\Join as JoinBuilder;

class ManyToMany extends Relation
{
    public function join(Query $query)
    {
        $pivotTable = $this->blueprint->getPivotTable();
        $primaryPivotKey = $pivotTable->getPrimaryKey();
        $localPivotKey = $this->blueprint->getLocalPivotKey();
        $foreignPivotKey = $this->blueprint->getForeignPivotKey();

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

        $query->getBuilder()->join($pivotJoinBuilder);

        return $this;
    }
}