<?php

namespace Stitch\Queries\Joins;

use Stitch\DBAL\Builders\Join as JoinBuilder;
use Stitch\DBAL\Builders\Table as TableBuilder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;

/**
 * Class ManyToMany
 * @package Stitch\Select\Relations
 */
class Pivoted extends Join
{
    /**
     * @param TableBuilder $tableBuilder
     */
    public function apply(TableBuilder $tableBuilder)
    {
        $pivotJoinBuilder = (new JoinBuilder($this->relation->getPivot()->getTable()))->type('LEFT');

        $pivotJoinBuilder->on(
                (new ColumnBuilder(
                    $this->relation->getLocalPivotKey()
                ))->table($pivotJoinBuilder),
                (new ColumnBuilder(
                    $this->relation->getLocalKey()
                ))->table($tableBuilder)
            );

        $this->builder->type('LEFT')
            ->on(
                (new ColumnBuilder(
                    $this->relation->getForeignKey()
                ))->table($this->builder),
                (new ColumnBuilder(
                    $this->relation->getForeignPivotKey()
                ))->table($pivotJoinBuilder)
            );

        $pivotJoinBuilder->join($this->builder);
        $tableBuilder->join($pivotJoinBuilder);
    }
}
