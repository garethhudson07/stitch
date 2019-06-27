<?php

namespace Stitch\Queries\Paths;

class Bag
{
    protected $relation;

    protected $column;

    public function setRelation(Path $relation)
    {
        $this->relation = $relation;

        return $this;
    }

    public function getRelation()
    {
        return $this->relation;
    }

    public function hasRelation()
    {
        return ($this->relation !== null);
    }

    public function setColumn(Column $column)
    {
        $this->column = $column;

        return $this;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function hasColumn()
    {
        return ($this->column !== null);
    }
}