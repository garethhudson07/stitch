<?php

namespace Stitch\Queries\Joins;

use Stitch\DBAL\Builders\Join as Builder;
use Stitch\DBAL\Builders\Table as TableBuilder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\Model;
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
}
