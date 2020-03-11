<?php

namespace Stitch\Relations;

use Closure;
use Stitch\Aggregate\Map;

/**
 * Class Collection
 * @package Stitch\Relations
 */
class Aggregate extends Map
{
    /**
     * @param string $name
     * @param Relation $relation
     * @return $this
     */
    public function add(Relation $relation)
    {
        $this->items[$relation->getName()] = $relation;

        return $this;
    }

    /**
     * @param $name
     * @param Closure $callback
     * @return $this
     */
    public function register($name, Closure $callback)
    {
        $this->items[$name] = $callback;

        return $this;
    }

    /**
     * @param string $name
     * @return Relation|null
     */
    public function get(string $name): ?Relation
    {
        return $this->has($name) ? $this->resolve($name) : null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function resolve(string $name)
    {
        if ($this->items[$name] instanceof Closure) {
            $this->items[$name] = $this->items[$name]();
        }

        return $this->items[$name];
    }
}