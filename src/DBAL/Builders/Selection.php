<?php

namespace Stitch\DBAL\Builders;

/**
 * Class Selection
 * @package Stitch\DBAL\Builders
 */
class Selection
{
    protected $columns = [];

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
        foreach ($this->columns as $added) {
            if ($column->matches($added)) {
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

    /**
     * @return int
     */
    public function count()
    {
        return count($this->columns);
    }
}
