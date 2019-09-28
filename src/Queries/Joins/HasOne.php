<?php

namespace Stitch\Queries\Joins;

use Stitch\Queries\Table;

/**
 * Class HasOne
 * @package Stitch\Select\Relations
 */
class HasOne extends Has
{
    public function apply()
    {
        parent::apply()->limit(1);
    }
}