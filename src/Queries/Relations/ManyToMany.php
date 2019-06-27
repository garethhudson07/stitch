<?php

namespace Stitch\Queries\Relations;

use Stitch\DBAL\Builders\Join;
use Stitch\DBAL\Builders\Join as JoinBuilder;
use Stitch\Queries\Query;
use Stitch\Schema\ForeignKey;
use Stitch\Schema\Table;

/**
 * Class ManyToMany
 * @package Stitch\Queries\Relations
 */
class ManyToMany extends Relation
{
    /**
     * @param Query $query
     * @return $this|mixed
     */
    public function join(Query $query)
    {
        /** @var Table $pivotTable */
        /** @var ForeignKey $localPivotKey */
        /** @var ForeignKey $foreignPivotKey */
        /** @var \Stitch\Relations\ManyToMany $blueprint */
        /** @var Join $builder */

        $blueprint = $this->blueprint;
        $builder = $this->builder;

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

        $builder->type('LEFT')
            ->on(
                "{$foreignPivotKey->getReferenceTableName()}.{$foreignPivotKey->getReferenceColumnName()}",
                '=',
                "{$pivotTable->getName()}.{$foreignPivotKey->getLocalColumn()->getName()}"
            );

        $pivotJoinBuilder->join($builder);

        $query->getBuilder()->join($pivotJoinBuilder);

        return $this;
    }
}