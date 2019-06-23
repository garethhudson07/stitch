<?php

namespace Stitch\Queries\Relations;

use Stitch\Queries\Query;
use Stitch\Model;
use Stitch\DBAL\Builders\Query as Builder;
use Stitch\Relations\Relation as Blueprint;

abstract class Relation extends Query
{
    protected $blueprint;

    public function __construct(Model $model, Builder $builder, Blueprint $blueprint)
    {
        parent::__construct($model, $builder);

        $this->blueprint = $blueprint;
    }

    abstract public function join(Query $query);
}