<?php

namespace Stitch\DBAL\Builders;
use Stitch\Schema\Column;

/**
 * Class Sorter
 * @package Stitch\DBAL\Builders
 */
class Sorter
{
    protected $bindings = [];

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @param string $path
     * @param string $direction
     * @return $this
     */
    public function bind(string $path, string $direction)
    {
        $this->bindings[$path] = $direction;

        return $this;
    }

    /**
     * @param Column $column
     * @param string $direction
     * @return $this
     */
    public function add(Column $column, string $direction)
    {
        $index = $this->indexOf($column);

        if ($index !== false) {
            $this->items[$index]['direction'] = $direction;
        } else {
            $this->items[] = [
                'column' => $column,
                'direction' => $direction
            ];
        }

        return $this;
    }

    /**
     * @param Column $column
     * @return bool|int|string
     */
    public function indexOf(Column $column)
    {
        foreach ($this->items as $key => $item) {
            if ($item['column'] === $column) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}