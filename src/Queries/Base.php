<?php

namespace Stitch\Queries;

use Stitch\Model;
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
