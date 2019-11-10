<?php

namespace Stitch\Queries;

use Stitch\Collection;
use Stitch\DBAL\Paths\Resolver as PathResolver;
use Stitch\Model;
use Stitch\DBAL\Builders\Query as Builder;
use Stitch\Queries\Joins\Collection as Joins;
use Stitch\DBAL\Dispatcher;
use Stitch\Records\Record;
use Stitch\Result\Blueprint as ResultBlueprint;
use Stitch\Result\Set as ResultSet;

/**
 * Class Query
 * @package Stitch\Select
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
     * @param string $pipeline
     * @return mixed
     */
    public function resolvePipeline(string $pipeline)
    {
        return $this->parsePipeline($pipeline)->resolve($this);
    }

    /**
     * @param mixed ...$pipelines
     * @return $this
     */
    public function with(...$pipelines)
    {
        foreach ($pipelines as $pipeline) {
            $this->joins->resolve(
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
                $this->resolvePipeline($pipeline)
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
            $this->resolvePipeline($pipeline),
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
            $this->joins->pull(
                $this->parsePipeline($arguments[0])
            )->limit($arguments[1]);
        } else {
            $this->builder->limit($arguments[0]);
        }

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return $this
     */
    public function offset(...$arguments)
    {
        if (count($arguments) > 1) {
            $this->joins->pull(
                $this->parsePipeline($arguments[0])
            )->offset($arguments[1]);
        } else {
            $this->builder->offset($arguments[0]);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function get()
    {
        $resultSet = $this->getResultSet();

        return $this->hydrate ? $resultSet->hydrate() : $resultSet;
    }

    /**
     * @return ResultSet
     */
    protected function getResultSet(): ResultSet
    {
        $paths = new PathResolver();

        return ResultBlueprint::make($this, $paths)->resultSet()->assemble(
            Dispatcher::select($this->builder, $paths)
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
