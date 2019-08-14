<?php

namespace Stitch\DBAL\Builders;

use Stitch\Schema\Column;

/**
 * Class Selection
 * @package Stitch\DBAL\Builders
 */
class Selection
{
    protected $bindings = [];

    protected $columns = [];

    /**
     * @param string $path
     * @return $this
     */
    public function bind(string $path)
    {
        if (!$this->hasBinding($path)) {
            $this->bindings[] = $path;
        }

        return $this;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function hasBinding(string $path)
    {
        return in_array($path, $this->bindings);
    }

    /**
     * @param Column $column
     * @return $this
     */
    public function add(Column $column)
    {
        if (!$this->hasColumn($column)) {
            $this->columns[] = $column;
        }

        return $this;
    }

    /**
     * @param Column $column
     * @return bool
     */
    public function hasColumn(Column $column)
    {
        return in_array($column, $this->columns);
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
