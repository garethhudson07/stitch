<?php

namespace Stitch\Queries\Joins;

use Stitch\DBAL\Builders\Join as Builder;
use Stitch\Model;
use Stitch\Relations\Relation as Blueprint;

/**
 * Class Relation
 * @package Stitch\Queries\Relations
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

        $this->apply();
    }

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
     * @param array ...$arguments
     * @return Query
     */
//    public function on(...$arguments)
//    {
//        return $this->applyOn('on', $arguments);
//    }

    /**
     * @param array ...$arguments
     * @return Query
     */
//    public function orOn(...$arguments)
//    {
//        return $this->applyOn('orOn', $arguments);
//    }

    /**
     * @param $type
     * @param array $arguments
     * @return $this
     */
//    protected function applyOn($type, array $arguments)
//    {
//        if ($arguments[1] instanceof Closure) {
//            $expression = new Expression($this);
//            $arguments[1]($expression);
//
//            $this->getRelation(PathFactory::split($arguments[0]))->getBuilder()->{$type}($expression);
//
//            return $this;
//        }
//
//        $path = array_shift($arguments);
//
//        if (count($arguments) == 1) {
//            $operator = '=';
//            $value = $arguments[0];
//        } else {
//            list($operator, $value) = $arguments;
//        }
//
//        $this->apply($path, function (Query $query, string $column) use ($type, $operator, $value)
//        {
//            $query->addCondition(
//                $type,
//                $column,
//                $operator,
//                $value
//            );
//        });
//
//        return $this;
//    }

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
    abstract public function apply();
}