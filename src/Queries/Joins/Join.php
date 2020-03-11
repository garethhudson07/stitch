<?php

namespace Stitch\Queries\Joins;

use Stitch\Aggregate\Map;
use Stitch\Aggregate\Set;
use Stitch\DBAL\Builders\Join as Builder;
use Stitch\DBAL\Builders\Table as TableBuilder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\Model;
use Stitch\Queries\Conditions\On;
use Stitch\Queries\Pipeline;
use Stitch\Relations\Relation;

/**
 * Class Relation
 * @package Stitch\Select\Relations
 */
class Join
{
    /**
     * @var Model
     */
    protected $model;

    protected $builder;

    /**
     * @var Relation
     */
    protected $relation;

    /**
     * @var array
     */
    protected $joins;

    protected $relations;

    protected $conditions;

    /**
     * Join constructor.
     * @param Model $model
     * @param Builder $builder
     * @param Relation $relation
     */
    public function __construct(Model $model, Builder $builder, Relation $relation)
    {
        $this->model = $model;
        $this->builder = $builder;
        $this->relation = $relation;
        $this->joins = new Map();
        $this->relations = new Set();
        $this->conditions = On::make($this->builder);
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return Map
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * @param mixed ...$pipelines
     * @return $this
     */
    public function with(Pipeline $pipeline)
    {
        $join = $pipeline->first();

        $this->relations->include($join->getRelation()->getName());

        if ($pipeline->count() > 1) {
            $join->with($pipeline->after(0));
        }

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return $this
     */
    public function on(...$arguments)
    {
        $this->conditions->and(...$arguments);

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return $this
     */
    public function orOn(...$arguments)
    {
        $this->conditions->or(...$arguments);

        return $this;
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
     * @param TableBuilder $tableBuilder
     */
    public function apply(TableBuilder $tableBuilder)
    {
        $this->builder->type('LEFT')
            ->on(
                (new ColumnBuilder(
                    $this->relation->getForeignKey()
                ))->table($this->builder),
                (new ColumnBuilder(
                    $this->relation->getLocalKey()
                ))->table($tableBuilder)
            );

        $tableBuilder->join($this->builder);
    }

    /**
     * @return Set
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
