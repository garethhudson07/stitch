<?php

namespace Stitch\Aggregate;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Stitch\Contracts\Arrayable;
use Traversable;

class Aggregate implements IteratorAggregate, ArrayAccess, Countable, Arrayable
{
    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

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
     * @param array $items
     * @return $this
     */
    public function fill(array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function unset($key)
    {
        $this->offsetUnset($key);

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->offsetExists($key) ? $this->items[$key] : null;
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Get the number of items.
     *
     * @return int
     */
    public function count(): int
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
