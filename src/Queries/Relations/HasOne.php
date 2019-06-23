<?php

namespace Stitch\Queries\Relations;

use Stitch\Queries\Query;

class HasOne extends Has
{
    public function join(Query $query)
    {
        return parent::join($query)->limit(1);
    }
}