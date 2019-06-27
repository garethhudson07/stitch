<?php

namespace Stitch\Relations;

use Closure;
use Stitch\Collection as BaseCollection;

/**
 * Class Collection
 * @package Stitch\Relations
 */
class Collection extends BaseCollection
{
    /**
     * @param string $name
     * @param Relation $relation
     * @return $this
     */
    public function add(string $name, Relation $relation)
    {
        $this->items[$name] = $relation;

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
     * @return bool
     */
    public function has(string $name)
    {
        return array_key_exists($name, $this->items);
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