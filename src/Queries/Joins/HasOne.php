<?php

namespace Stitch\Queries\Joins;

use Stitch\DBAL\Builders\Table as TableBuilder;

/**
 * Class HasOne
 * @package Stitch\Select\Relations
 */
class HasOne extends Has
{
    public function apply(TableBuilder $tableBuilder)
    {
        parent::apply($tableBuilder)->limit(1);
    }
}
