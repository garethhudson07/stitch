<?php

namespace Stitch\DBAL\Builders;

use Aggregate\Set;

class JsonPath extends Set
{
    protected $column;

    /**
     * @param Column $column
     * @return $this
     */
    public function column(Column $column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return Column
     */
    public function getColumn(): Column
    {
        return $this->column;
    }
}
