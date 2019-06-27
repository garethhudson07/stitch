<?php

namespace Stitch\Queries\Relations;

use Stitch\DBAL\Builders\Join;
use Stitch\Queries\Query;

/**
 * Class Has
 * @package Stitch\Queries\Relations
 */
class Has extends Relation
{
    /**
     * @param Query $query
     * @return $this|mixed
     */
    public function join(Query $query)
    {
        /** @var \Stitch\Relations\Has $blueprint */
        /** @var Join $builder */

        $blueprint = $this->blueprint;
        $builder = $this->builder;

        $foreignKey = $blueprint->getForeignKey();

        $builder->type('LEFT')
            ->on(
                "{$blueprint->getForeignModel()->getTable()->getName()}.{$foreignKey->getLocalColumn()->getName()}",
                '=',
                "{$foreignKey->getReferenceTableName()}.{$foreignKey->getReferenceColumnName()}"
            );

        $query->getBuilder()->join($builder);

        return $this;
    }
}