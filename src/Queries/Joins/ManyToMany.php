<?php

namespace Stitch\Queries\Joins;

use Stitch\DBAL\Builders\Join as JoinBuilder;
use Stitch\DBAL\Builders\Table as TableBuilder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;

/**
 * Class ManyToMany
 * @package Stitch\Select\Relations
 */
class ManyToMany extends Join
{
    /**
     * @param TableBuilder $tableBuilder
     */
    public function apply(TableBuilder $tableBuilder)
    {
        $pivotJoinBuilder = (new JoinBuilder($this->blueprint->getPivotTable()))
            ->type('LEFT')
            ->on(
                new ColumnBuilder($this->blueprint->getLocalPivotKey()),
                new ColumnBuilder($this->blueprint->getLocalKey())
            );

        $this->builder->type('LEFT')
            ->on(
                new ColumnBuilder($this->blueprint->getForeignKey()),
                new ColumnBuilder($this->blueprint->getForeignPivotKey())
            );

        $pivotJoinBuilder->join($this->builder);
        $tableBuilder->join($pivotJoinBuilder);
    }
}
