<?php

namespace Stitch\Queries\Relations;

use Stitch\Queries\Query;

class Has extends Relation
{
    public function join(Query $query)
    {
        $foreignKey = $this->blueprint->getForeignKey();

        $this->builder->type('LEFT')
            ->on(
                "{$this->blueprint->getForeignModel()->getTable()->getName()}.{$foreignKey->getLocalColumn()->getName()}",
                '=',
                "{$foreignKey->getReferenceTableName()}.{$foreignKey->getReferenceColumnName()}"
            );

        $query->getBuilder()->join($this->builder);

        return $this;
    }
}