<?php

namespace Stitch\Queries\Relations;

use Stitch\Queries\Query;

/**
 * Class HasOne
 * @package Stitch\Queries\Relations
 */
class HasOne extends Has
{
    /**
     * @param Query $query
     * @return mixed|Has
     */
    public function join(Query $query)
    {
        return parent::join($query)->limit(1);
    }
}