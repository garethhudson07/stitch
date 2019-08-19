<?php

namespace Stitch\Queries;

use Closure;
use Stitch\Model;
use Stitch\Queries\Paths\Factory as PathFactory;
use Stitch\Queries\Paths\Path;
use Stitch\Queries\Joins\Join;

/**
 * Class Query
 * @package Stitch\Queries
 */
class Base
{
    /**
     * @var Model
     */
    protected $model;

    protected $builder;

    protected $pathFactory;

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * Base constructor.
     * @param Model $model
     * @param $builder
     */
    public function __construct(Model $model, $builder)
    {
        $this->model = $model;
        $this->builder = $builder;
        $this->pathFactory = new PathFactory($model);
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
     * @param Path $path
     * @return Query
     */
    public function join(Path $path)
    {
        $name = $path->first();

        if (!array_key_exists($name, $this->joins)) {
            $this->addJoin($name, $this->model->getRelation($name)->join());
        }

        if ($path->count() > 1) {
            return $this->joins[$name]->join($path->after(0));
        }

        return $this->joins[$name];
    }

    /**
     * @param string $name
     * @param Join $join
     * @return $this
     */
    public function addJoin(string $name, Join $join)
    {
        $this->joins[$name] = $join->apply($this);

        return $this;
    }

    /**
     * @param Path $path
     * @return Query
     */
    public function getJoin(Path $path)
    {
        $join = $this->joins[$path->first()];

        if ($path->count() > 1) {
            return $join->getJoin($path->after(0));
        }

        return $join;
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
     * @param $number
     * @return $this
     */
    public function setLimit($number)
    {
        $this->builder->limit($number);

        return $this;
    }

    /**
     * @return array
     */
    public function getJoins()
    {
        return $this->joins;
    }
}
