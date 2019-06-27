<?php

namespace Stitch\DBAL\Builders;

/**
 * Class Sorter
 * @package Stitch\DBAL\Builders
 */
class Sorter
{
    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @param string $name
     * @param string $direction
     * @return $this
     */
    public function add(string $name, string $direction)
    {
        $index = $this->indexOf($name);

        if ($index !== false) {
            $this->columns[$index]['direction'] = $direction;
        } else {
            $this->columns[] = [
                'name' => $name,
                'direction' => $direction
            ];
        }

        return $this;
    }

    /**
     * @param string $name
     * @return bool|int|string
     */
    public function indexOf(string $name)
    {
        foreach ($this->columns as $key => $column) {
            if ($column['name'] === $name) {
                return $key;
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

    /**
     * @return int
     */
    public function count()
    {
        return count($this->columns);
    }
}