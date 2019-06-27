<?php

namespace Stitch;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Stitch\Contracts\Arrayable;

/**
 * Class Collection
 * @package Stitch
 */
class Collection implements IteratorAggregate, ArrayAccess, Countable, Arrayable
{

    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new collection.
     *
     * @param mixed $items
     * @return void
     */
    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * @param $value
     * @return $this
     */
    public function push($value)
    {
        $this->items[] = $value;

        return $this;
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        if ($this->items instanceof Collection) {
            return $this->items->offsetExists($key);
        }

        return array_key_exists($key, $this->items);
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return isset($this->items[$key]) ? $this->items[$key] : null;
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        if ($this->items instanceof Collection) {
            return new ArrayIterator($this->items->getIterator());
        }

        return new ArrayIterator($this->items);
    }

    /**
     * Get the number of items.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Convert the collection to json.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function ($item) {
            return $item instanceof Arrayable ? $item->toArray() : $item;
        }, $this->items);
    }
}
