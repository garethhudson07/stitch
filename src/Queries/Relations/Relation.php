<?php

namespace Stitch\Queries\Relations;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\Model;
use Stitch\Queries\Query;
use Stitch\Relations\Relation as Blueprint;

/**
 * Class Relation
 * @package Stitch\Queries\Relations
 */
abstract class Relation extends Query
{
    /**
     * @var Blueprint
     */
    protected $blueprint;

    /**
     * Relation constructor.
     * @param Model $model
     * @param Builder $builder
     * @param Blueprint $blueprint
     */
    public function __construct(Model $model, Builder $builder, Blueprint $blueprint)
    {
        parent::__construct($model, $builder);

        $this->blueprint = $blueprint;
    }

    /**
     * @param Query $query
     * @return mixed
     */
    abstract public function join(Query $query);
}