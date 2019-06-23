<?php

namespace Stitch\DBAL\Builders;

use Closure;

class Selection
{
    protected $columns = [];

    public function __construct(array $items = [])
    {
        $this->unpack($items);
    }

    public function add(string $name, ?Closure $callback = null)
    {
        if ( ! $this->has($name)) {
            $column = new Column($name);

            if ($callback) {
                $callback($column);
            }

            $this->columns[] = $column;
        }

        return $this;
    }

    public function has(string $name)
    {
        foreach ($this->columns as $column) {
            if ($column->getName() === $name) {
                return true;
            }
        }

        return false;
    }

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

    public function getColumns()
    {
        return $this->columns;
    }
}