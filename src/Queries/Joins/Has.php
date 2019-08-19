<?php

namespace Stitch\Queries\Joins;

use Stitch\Queries\Base;

/**
 * Class Has
 * @package Stitch\Queries\Relations
 */
class Has extends Join
{
    /**
     * @param Base $base
     * @return $this|mixed
     */
    public function apply(Base $base)
    {
        $blueprint = $this->blueprint;
        $builder = $this->builder;

        $foreignKey = $blueprint->getForeignKey();

        $builder->type('LEFT')
            ->on(
                "{$blueprint->getForeignModel()->getTable()->getName()}.{$foreignKey->getLocalColumn()->getName()}",
                '=',
                "{$foreignKey->getReferenceTableName()}.{$foreignKey->getReferenceColumnName()}"
            );

        $base->getBuilder()->join($builder);

        return $this;
    }
}