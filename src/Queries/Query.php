<?php

namespace Stitch\Queries;

use Closure;
use Stitch\Collection;
use Stitch\DBAL\Builders\Column;
use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Dispatcher;
use Stitch\Model;
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
     * @param array ...$arguments
     * @return Query
     */
    public function orWhere(...$arguments)
    {
        return $this->applyWhere('orWhere', $arguments);
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
