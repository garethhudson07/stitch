<?php

namespace Stitch\Relations;

use Stitch\Queries\Relations\HasOne as Query;

/**
 * Class HasOne
 * @package Stitch\Relations
 */
class HasOne extends Has
{
    /**
     * @return string
     */
    public function queryClass()
    {
        return Query::class;
    }
}