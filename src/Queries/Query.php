<?php

namespace Stitch\Queries;

use Closure;
use Stitch\Collection;
use Stitch\DBAL\Builders\Column;
use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Dispatcher;
use Stitch\Model;
use Stitch\Queries\Paths\Factory as PathFactory;
use Stitch\Queries\Paths\Path;
use Stitch\Queries\Relations\Relation;
use Stitch\Result\Hydrator as ResultHydrator;
use Stitch\Result\Set as ResultSet;

/**
 * Class Query
 * @package Stitch\Queries
 */
class Query
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * Query constructor.
     * @param Model $model
     * @param Builder $builder
     */
    public function __construct(Model $model, Builder $builder)
    {
        $this->model = $model;
        $this->builder = $builder;
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @param array ...$relations
     * @return $this
     */
    public function with(...$relations)
    {
        foreach ($relations as $relation) {
            $this->makeRelation(PathFactory::split($relation));
        }

        return $this;
    }

    /**
     * @param Path $path
     * @return Query
     */
    public function makeRelation(Path $path)
    {
        $name = $path->first();

        if (!array_key_exists($name, $this->relations)) {
            $this->addRelation($name, $this->model->getRelation($name)->query());
        }

        if ($path->count() > 1) {
            return $this->relations[$name]->makeRelation($path->after(0));
        }

        return $this->relations[$name];
    }

    /**
     * @param string $name
     * @param Relation $relation
     * @return $this
     */
    public function addRelation(string $name, Relation $relation)
    {
        $this->relations[$name] = $relation->join($this);

        return $this;
    }

    /**
     * @param array ...$paths
     * @return $this
     */
    public function select(...$paths)
    {
        foreach ($paths as $path) {
            $this->apply($path, function (Query $query, string $column) {
                $query->addColumn($column);
            });
        }

        return $this;
    }

    /**
     * @param string $path
     * @param Closure $callback
     */
    protected function apply(string $path, Closure $callback)
    {
        $bag = PathFactory::divide($this, $path);
        $column = $bag->getColumn()->implode();

        if ($bag->hasRelation()) {
            $callback($this->getRelation($bag->getRelation()), $column);
        } else {
            $callback($this, $column);
        }
    }

    /**
     * @param Path $path
     * @return Query
     */
    public function getRelation(Path $path)
    {
        if (!array_key_exists($path->first(), $this->relations)) {
            return $this->makeRelation($path);
        }

        $relation = $this->relations[$path->first()];

        if ($path->count() > 1) {
            return $relation->getRelation($path->after(0));
        }

        return $relation;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function addColumn(string $name)
    {
        $table = $this->model->getTable()->getName();

        $this->builder->select($name, function (Column $column) use ($table, $name) {
            $column->alias("{$table}_{$name}");
        });

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return Query
     */
    public function where(...$arguments)
    {
        return $this->applyWhere('where', $arguments);
    }

    /**
     * @param $type
     * @param array $arguments
     * @return $this
     */
    protected function applyWhere($type, array $arguments)
    {
        if ($arguments[0] instanceof Closure) {
            $expression = new Expression($this);
            $arguments[0]($expression);

            $this->builder->{$type}($expression);

            return $this;
        }

        $path = array_shift($arguments);

        if (count($arguments) == 1) {
            $operator = '=';
            $value = $arguments[0];
        } else {
            list($operator, $value) = $arguments;
        }

        return $this->addCondition($type, $this->translatePath($path), $operator, $value);
    }

    /**
     * @param string $type
     * @param string $path
     * @param string $operator
     * @param $value
     * @return $this
     */
    public function addCondition(string $type, string $path, string $operator, $value)
    {
        $this->builder->{$type}(
            $path,
            $operator,
            $value
        );

        return $this;
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
     * @param string $path
     * @return string
     */
    public function translatePath(string $path)
    {
        $bag = PathFactory::divide($this, $path);
        $column = $bag->getColumn()->implode();

        if ($bag->hasRelation()) {
            $table = $this->getRelation($bag->getRelation())->getModel()->getTable()->getName();
        } else {
            $table = $this->model->getTable()->getName();
        }

        return "$table.$column";
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array ...$arguments
     * @return Query
     */
    public function orWhere(...$arguments)
    {
        return $this->applyWhere('orWhere', $arguments);
    }

    /**
     * @param string $path
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $path, string $direction = 'ASC')
    {
        $this->builder->orderBy($this->translatePath($path), $direction);

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return $this
     */
    public function limit(...$arguments)
    {
        if (count($arguments) > 1) {
            $this->getRelation(PathFactory::split($arguments[0]))->setLimit($arguments[1]);
        } else {
            $this->setLimit($arguments[0]);
        }

        return $this;
    }

    /**
     * @param $number
     * @return $this
     */
    public function setLimit($number)
    {
        $this->builder->limit($number);

        return $this;
    }

    /**
     * @return Collection
     */
    public function get()
    {
        $this->forceSelection();

        return (new ResultHydrator($this->model))->hydrate(
            new ResultSet(
                $this,
                Dispatcher::select($this->builder)
            )
        );
    }

    /**
     * @return $this
     */
    public function forceSelection()
    {
        count($this->builder->getSelection()->getColumns()) ? $this->selectPrimaryKey() : $this->selectAll();

        foreach ($this->relations as $relation) {
            $relation->forceSelection();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function selectPrimaryKey()
    {
        $primaryKeyName = $this->model->getTable()->getPrimaryKey()->getName();

        if (!$this->builder->getSelection()->has($primaryKeyName)) {
            $this->addColumn($primaryKeyName);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function selectAll()
    {
        foreach ($this->model->getTable()->getColumns() as $column) {
            /** @var \Stitch\Schema\Column $column */
            $this->addColumn($column->getName());
        }

        foreach ($this->relations as $relation) {
            $relation->selectAll();
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
