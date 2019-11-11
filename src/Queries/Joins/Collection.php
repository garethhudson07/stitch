<?php

namespace Stitch\Queries\Joins;

use Stitch\Collection as BaseCollection;
use Stitch\DBAL\Builders\Table as Builder;
use Stitch\Queries\Pipeline;

class Collection extends BaseCollection
{
    protected $items = [];

    protected $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param Pipeline $pipeline
     * @return mixed
     */
    public function resolve(Pipeline $pipeline)
    {
        $relation = $pipeline->first();
        $name = $relation->getName();

        if (!array_key_exists($name, $this->items)) {
            $this->add($name, $relation->join());
        }

        if ($pipeline->count() > 1) {
            return $this->items[$name]->getJoins()->resolve($pipeline->after(0));
        }

        return $this->items[$name];
    }

    /**
     * @param string $name
     * @param Join $join
     * @return $this
     */
    public function add(string $name, Join $join)
    {
        $join->apply($this->builder);

        $this->items[$name] = $join;

        return $this;
    }

    /**
     * @param Pipeline $pipeline
     * @return mixed
     */
    public function pull(Pipeline $pipeline)
    {
        $join = $this->items[$pipeline->first()->getName()];

        if ($pipeline->count() > 1) {
            return $join->getJoins()->pull($pipeline->after(0));
        }

        return $join;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }
}