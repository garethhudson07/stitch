<?php

namespace Stitch\Queries;

use Stitch\Collection;
use Stitch\Model;
use Stitch\DBAL\Builders\Query as Builder;
use Stitch\Queries\Joins\Collection as Joins;
use Stitch\DBAL\Dispatcher;
use Stitch\Records\Record;
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

    protected $builder;

    /**
     * @var array
     */
    protected $joins;

    protected $conditions;

    protected $hydrate = true;

    /**
     * Base constructor.
     * @param Model $model
     * @param $builder
     */
    public function __construct(Model $model, Builder $builder)
    {
        $this->model = $model;
        $this->builder = $builder;
        $this->joins = new Joins($builder);
        $this->conditions = new Expression($this, $builder->getConditions());
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return mixed
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return array|Joins
     */
    public function getJoins()
    {
        return $this->joins;
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
     * @param string $pipeline
     * @return Pipeline
     */
    public function parsePipeline(string $pipeline)
    {
        return Pipeline::parse($this->model, $pipeline);
    }

    /**
     * @param array ...$relations
     * @return $this
     */
    public function with(...$pipelines)
    {
        foreach ($pipelines as $pipeline) {
            $this->joins->push(
                $this->parsePipeline($pipeline)
            );
        }

        return $this;
    }

    /**
     * @param array ...$pipelines
     * @return $this
     */
    public function select(...$pipelines)
    {
        foreach ($pipelines as $pipeline) {
            $this->builder->select(
                $this->parsePipeline($pipeline)->last()
            );
        }

        return $this;
    }

    /**
     * @param string $pipeline
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $pipeline, string $direction = 'ASC')
    {
        $this->builder->orderBy(
            $this->parsePipeline($pipeline)->last(),
            $direction
        );

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return Query
     */
    public function where(...$arguments)
    {
        $this->conditions->where(...$arguments);

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return Query
     */
    public function orWhere(...$arguments)
    {
        $this->conditions->orWhere(...$arguments);

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return $this
     */
    public function limit(...$arguments)
    {
        if (count($arguments) > 1) {
            $this->joins->get(
                $this->parsePipeline($arguments[0])
            )->setLimit($arguments[1]);
        } else {
            $this->builder->limit($arguments[0]);
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
            Dispatcher::select($this->model->getConnection(), $this->builder)
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
