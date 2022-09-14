<?php

namespace Stitch\Queries;

use Closure;
use Aggregate\Map;
use Aggregate\Set;
use Stitch\DBAL\Paths\Resolver as PathResolver;
use Stitch\Model;
use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Dispatcher;
use Stitch\Queries\Conditions\Where;
use Stitch\Result\Record;
use Stitch\Records\Record as HydratedRecord;
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

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Map
     */
    protected $joins;

    /**
     * @var Set
     */
    protected $relations;

    /**
     * @var Emitter
     */
    protected $emitter;

    /**
     * @var Where
     */
    protected $conditions;

    /**
     * @var bool
     */
    protected $hydrate = true;

    /**
     * @param Model $model
     * @param Builder $builder
     * @param Map $joins
     * @param Set $relations
     * @param Emitter $emitter
     */
    public function __construct(Model $model, Builder $builder, Map $joins, Set $relations, Emitter $emitter)
    {
        $this->model = $model;
        $this->builder = $builder;
        $this->joins = $joins;
        $this->relations = $relations;
        $this->emitter = $emitter;
        $this->conditions = new Where($this, $builder->getConditions());
    }

    /**
     * @param Model $model
     * @param Builder $builder
     * @return Query
     */
    public static function make(Model $model, Builder $builder): Query
    {
        $joins = new Map();

        return new static(
            $model,
            $builder,
            $joins,
            new Set(),
            Emitter::make($model, $joins, Path::make())
        );
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
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
    public function getJoins(): Map
    {
        return $this->joins;
    }

    /**
     * @return static
     */
    public function hydrated()
    {
        $this->hydrate = true;

        return $this;
    }

    /**
     * @return static
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
    public function buildPipeline(string $pipeline)
    {
        return Pipeline::build($this, $pipeline);
    }

    /**
     * @param mixed ...$pipelines
     * @return static
     */
    public function with(...$pipelines)
    {
        foreach ($pipelines as $pipeline) {
            $pipeline = $this->buildPipeline($pipeline);
            $join = $pipeline->first();

            $this->relations->include($join->getRelation()->getName());

            if ($pipeline->count() > 1) {
                $join->with($pipeline->after(0));
            }
        }

        return $this;
    }

    /**
     * @param array ...$pipelines
     * @return static
     */
    public function select(...$pipelines)
    {
        foreach ($pipelines as $pipeline) {
            $this->builder->select(
                $this->buildPipeline($pipeline)->last()
            );
        }

        return $this;
    }

    /**
     * @param string $pipeline
     * @param string $direction
     * @return static
     */
    public function orderBy(string $pipeline, string $direction = 'ASC')
    {
        $this->builder->orderBy(
            $this->buildPipeline($pipeline)->last(),
            $direction
        );

        return $this;
    }

    /**
     * @param ...$arguments
     * @return Query
     */
    public function where(...$arguments)
    {
        $this->conditions->and(...$arguments);

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return Query
     */
    public function orWhere(...$arguments)
    {
        $this->conditions->or(...$arguments);

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return Query
     */
    public function on(...$arguments)
    {
        $pipeline = $this->buildPipeline(array_shift($arguments));

        if ($arguments[0] instanceof Closure) {
            $pipeline->last()->on(...$arguments);

            return $this;
        }

        $pipeline->penultimate()->on(
            ...array_merge([$pipeline->last()], $arguments)
        );

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return Query
     */
    public function orOn(...$arguments)
    {
        $pipeline = $this->buildPipeline(array_shift($arguments));

        if ($arguments[0] instanceof Closure) {
            $pipeline->last()->orOn(...$arguments);

            return $this;
        }

        $pipeline->penultimate()->orOn(
            ...array_merge([$pipeline->last()], $arguments)
        );

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return static
     */
    public function limit(...$arguments)
    {
        if (count($arguments) > 1) {
            $this->buildPipeline($arguments[0])->last()->limit($arguments[1]);
        } else {
            $this->builder->limit($arguments[0]);
        }

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return static
     */
    public function offset(...$arguments)
    {
        if (count($arguments) > 1) {
            $this->buildPipeline($arguments[0])->last()->offset($arguments[1]);
        } else {
            $this->builder->offset($arguments[0]);
        }

        return $this;
    }

    /**
     * @return ResultSet
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

        $this->emitter->fetching($this);

        $result = ResultBlueprint::make($this, $paths)->resultSet()->assemble(
            Dispatcher::select($this->builder, $paths)
        );

        $this->emitter->fetched($this, $result);

        return $result;
    }

    /**
     * @return Record|HydratedRecord|null
     */
    public function first()
    {
        $result = $this->limit(1)->get();

        return $result->count() ? $result[0] : null;
    }

    /**
     * @return Set
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
