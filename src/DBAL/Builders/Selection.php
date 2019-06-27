<?php

namespace Stitch\DBAL\Builders;

use Closure;

/**
 * Class Selection
 * @package Stitch\DBAL\Builders
 */
class Selection
{
    /**
     * @var array
     */
    protected $columns = [];

    /**
     * Selection constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->unpack($items);
    }

    /**
     * @param array $items
     * @return $this
     */
    public function unpack(array $items)
    {
        if (count($items) === 2 && $items[1] instanceof Closure) {
            $this->add(...$items);
        } else {
            foreach ($items as $key => $item) {
                is_array($item) ? $this->add(...$item) : $this->add($item);
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @param Closure|null $callback
     * @return $this
     */
    public function add(string $name, ?Closure $callback = null)
    {
        if (!$this->has($name)) {
            $column = new Column($name);

            if ($callback) {
                $callback($column);
            }

            $this->columns[] = $column;
        }

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        foreach ($this->columns as $column) {
            if ($column->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }
}