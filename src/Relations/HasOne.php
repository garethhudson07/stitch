<?php

namespace Stitch\Relations;

use Stitch\Queries\Relations\HasOne as Query;

class HasOne extends Has
{
    public function queryClass()
    {
        return Query::class;
    }
}