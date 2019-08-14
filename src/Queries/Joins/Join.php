<?php

namespace Stitch\Queries\Joins;

use Stitch\DBAL\Builders\Join as Builder;
use Stitch\Model;
use Stitch\Queries\Base;
use Stitch\Relations\Relation as Blueprint;

/**
 * Class Relation
 * @package Stitch\Queries\Relations
 */
abstract class Join extends Base
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
     * @return Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

    /**
     * @param Base $query
     * @return mixed
     */
    abstract public function apply(Base $query);
}