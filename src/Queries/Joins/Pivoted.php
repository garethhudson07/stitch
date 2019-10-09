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
        $pivotJoinBuilder = (new JoinBuilder($this->relation->getPivotTable()))
            ->type('LEFT')
            ->on(
                new ColumnBuilder($this->relation->getLocalPivotKey()),
                new ColumnBuilder($this->relation->getLocalKey())
            );

        $this->builder->type('LEFT')
            ->on(
                new ColumnBuilder($this->relation->getForeignKey()),
                new ColumnBuilder($this->relation->getForeignPivotKey())
            );

        $pivotJoinBuilder->join($this->builder);
        $tableBuilder->join($pivotJoinBuilder);
    }
}
