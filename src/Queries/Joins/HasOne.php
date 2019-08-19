<?php

namespace Stitch\Queries\Joins;

use Stitch\Queries\Base;

/**
 * Class HasOne
 * @package Stitch\Queries\Relations
 */
class HasOne extends Has
{
    /**
     * @param Base $query
     * @return mixed|Has
     */
    public function apply(Base $query)
    {
        return parent::apply($query)->limit(1);
    }
}