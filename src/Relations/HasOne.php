<?php

namespace Stitch\Relations;

use Stitch\Queries\Relations\HasOne as Query;
use Stitch\Records\Relations\BelongsTo;

/**
 * Class HasOne
 * @package Stitch\Relations
 */
class HasOne extends Has
{
    /**
     * @return Query
     */
    public function query()
    {
        return new Query($this->getForeignModel(), $this->joinBuilder(), $this);
    }

    /**
     * @return mixed|BelongsTo|\Stitch\Records\Relations\Collection
     */
    public function make()
    {
        return $this->record();
    }
}