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
use Stitch\Records\Record;
use Stitch\Result\Hydrator as ResultHydrator;
use Stitch\Result\Set as ResultSet;

/**
 * Class Query
 * @package Stitch\Queries
 */
class Query extends Base
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Builder
     */
    protected $builder;

    protected $pathFactory;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var bool
     */
    protected $hydrate = true;

    /**
     * Query constructor.
     * @param Model $model
     * @param Builder $builder
     */
    public function __construct(Model $model, Builder $builder)
    {
        parent::__construct($model, $builder);

        $this->pathFactory = new PathFactory($model);
    }

    /**
     * @return $this
     */
    public function hydrated()
    {
        $this->hydrate = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function dehydrated()
    {
        $this->hydrate = false;

        return $this;
    }

    /**
     * @param array ...$relations
     * @return $this
     */
    public function with(...$relations)
    {
        foreach ($relations as $relation) {
            $this->join($this->pathFactory->explode($relation));
        }

        return $this;
    }

    /**
     * @param array ...$paths
     * @return $this
     */
    public function select(...$paths)
    {
        foreach ($paths as $path) {
            $resolved = $this->resolvePath($path);

            $this->builder->select(
                $this->assemblePath($resolved['table'], $resolved['column'])
            );
        }

        return $this;
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
     * @param string $path
     * @param Closure $callback
     */
    protected function resolve(string $path, Closure $callback)
    {
        $bag = $this->pathFactory->split($path);
        $column = $bag->getColumn()->implode();

        if ($bag->hasRelation()) {
            $callback($this->getJoin($bag->getRelation()), $column);
        } else {
            $callback($this, $column);
        }
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
            return $this->applyWhereExpression($type, $arguments[0]);
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
     * @param $type
     * @param Closure $callback
     * @return $this
     */
    protected function applyWhereExpression($type, Closure $callback)
    {
        $expression = new Expression($this);
        $callback($expression);

        $this->builder->{$type}($expression);

        return $this;
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
     * @param string $path
     * @return array
     */
    protected function resolvePath(string $path)
    {
        $bag = $this->pathFactory->split($path);

        return [
            'table' => $bag->hasRelation() ?
                $this->getJoin($bag->getRelation())->getModel()->getTable()->getName() :
                $this->model->getTable()->getName(),
            'column' => $bag->getColumn()->implode()
        ];
    }

    /**
     * @param string $path
     * @return string
     */
    public function translatePath(string $path)
    {
        $resolved = $this->resolvePath($path);

        return $this->assemblePath($resolved['table'], $resolved['column']);
    }

    /**
     * @param string $table
     * @param string $column
     * @return string
     */
    public function assemblePath(string $table, string $column)
    {
        return "$table.$column";
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
     * @param array ...$arguments
     * @return $this
     */
    public function limit(...$arguments)
    {
        if (count($arguments) > 1) {
            $this->getJoin($this->pathFactory->explode($arguments[0]))->setLimit($arguments[1]);
        } else {
            $this->setLimit($arguments[0]);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function get()
    {
        $resultSet = $this->getResultSet();

        if (!$this->hydrate) {
            return $resultSet;
        }

        return ResultHydrator::hydrate($this->model->collection(), $resultSet);
    }

    /**
     * @return ResultSet
     */
    protected function getResultSet(): ResultSet
    {
        return new ResultSet(
            $this,
            Dispatcher::select($this->model->getConnection(), $this->builder->resolve())
        );
    }

    /**
     * @return Record|null
     */
    public function first()
    {
        $result = $this->limit(1)->get();

        return $result->count() ? $result[0] : null;
    }
}
