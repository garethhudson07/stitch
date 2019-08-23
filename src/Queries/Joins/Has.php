<?php

namespace Stitch\Queries\Joins;

use Stitch\Queries\Table;

/**
 * Class Has
 * @package Stitch\Queries\Relations
 */
class Has extends Join
{
    public function apply()
    {
        $blueprint = $this->blueprint;
        $foreignKey = $blueprint->getForeignKey();

        $this->builder->type('LEFT')
            ->on(
                "{$blueprint->getForeignModel()->getTable()->getName()}.{$foreignKey->getLocalColumn()->getName()}",
                '=',
                "{$foreignKey->getReferenceTableName()}.{$foreignKey->getReferenceColumnName()}"
            );
    }
}