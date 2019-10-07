<?php

namespace Stitch\Queries\Joins;

use Stitch\DBAL\Builders\Join as Builder;
use Stitch\DBAL\Builders\Table as TableBuilder;
use Stitch\Model;
use Stitch\Relations\Relation as Blueprint;

/**
 * Class Relation
 * @package Stitch\Select\Relations
 */
abstract class Join
{
    /**
     * @var Model
     */
    protected $model;

    protected $builder;

    /**
     * @var Blueprint
     */
    protected $blueprint;

    /**
     * @var array
     */
    protected $joins;

    /**
     * Relation constructor.
     * @param Model $model
     * @param Builder $builder
     * @param Blueprint $blueprint
     */
    public function __construct(Model $model, Builder $builder, Blueprint $blueprint)
    {
        $this->model = $model;
        $this->builder = $builder;
        $this->blueprint = $blueprint;
        $this->joins = new Collection($builder);
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return array|Collection
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function limit(int $count)
    {
        $this->builder->limit($count);

        return $this;
    }

    /**
     * @param int $start
     * @return $this
     */
    public function offset(int $start)
    {
        $this->builder->offset($start);

        return $this;
    }

    /**
     * @return mixed
     */
    abstract public function apply(TableBuilder $tableBuilder);
}