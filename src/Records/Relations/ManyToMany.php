<?php

namespace Stitch\Records\Relations;

use Stitch\Model;
use Stitch\Records\Record;
use Stitch\Relations\ManyToMany as Blueprint;

class ManyToMany extends Record
{
    protected $blueprint;

    /**
     * BelongsTo constructor.
     * @param Model $model
     * @param Blueprint $blueprint
     */
    public function __construct(Model $model, Blueprint $blueprint)
    {
        parent::__construct($model);


        $this->blueprint = $blueprint;
    }

}
