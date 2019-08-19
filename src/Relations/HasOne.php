<?php

namespace Stitch\Relations;

use Stitch\Queries\Joins\HasOne as Join;
use Stitch\Records\Relations\BelongsTo;

/**
 * Class HasOne
 * @package Stitch\Relations
 */
class HasOne extends Has
{
    /**
     * @return mixed|\Stitch\Queries\Joins\Has|Join
     */
    public function join()
    {
        return new Join($this->getForeignModel(), $this->joinBuilder(), $this);
    }

    /**
     * @return mixed|BelongsTo|\Stitch\Records\Relations\Collection
     */
    public function make()
    {
        return $this->record();
    }
}