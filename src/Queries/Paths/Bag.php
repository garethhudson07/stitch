<?php

namespace Stitch\Queries\Paths;

/**
 * Class Bag
 * @package Stitch\Queries\Paths
 */
class Bag
{
    /**
     * @var
     */
    protected $relation;

    /**
     * @var
     */
    protected $column;

    /**
     * @return mixed
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param Path $relation
     * @return $this
     */
    public function setRelation(Path $relation)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasRelation()
    {
        return ($this->relation !== null);
    }

    /**
     * @return mixed
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param Column $column
     * @return $this
     */
    public function setColumn(Column $column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasColumn()
    {
        return ($this->column !== null);
    }
}