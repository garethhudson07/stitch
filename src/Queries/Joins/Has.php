<?php

namespace Stitch\Queries\Joins;

use Stitch\DBAL\Builders\Table as TableBuilder;

/**
 * Class Has
 * @package Stitch\Select\Relations
 */
class Has extends Join
{
    /**
     * @param TableBuilder $tableBuilder
     */
    public function apply(TableBuilder $tableBuilder)
    {
        $this->builder->type('LEFT')
            ->localKey($this->blueprint->getLocalKey())
            ->foreignKey($this->blueprint->getForeignKey());

        $tableBuilder->join($this->builder);
    }
}
