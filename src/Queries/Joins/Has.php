<?php

namespace Stitch\Queries\Joins;

use Stitch\DBAL\Builders\Table as TableBuilder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;

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
            ->on(
                new ColumnBuilder($this->blueprint->getForeignKey()),
                new ColumnBuilder($this->blueprint->getLocalKey())
            );

        $tableBuilder->join($this->builder);
    }
}
