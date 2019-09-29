<?php

namespace Stitch\Queries\Joins;

use Stitch\DBAL\Builders\Join as JoinBuilder;
use Stitch\DBAL\Builders\Table as TableBuilder;

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
            ->localKey($this->blueprint->getLocalKey())
            ->foreignKey($this->blueprint->getLocalPivotKey());

        $this->builder->type('LEFT')
            ->localKey($this->blueprint->getForeignPivotKey())
            ->foreignKey($this->blueprint->getForeignKey());

        $pivotJoinBuilder->join($this->builder);
        $tableBuilder->join($pivotJoinBuilder);
    }
}
