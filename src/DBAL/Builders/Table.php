<?php

namespace Stitch\DBAL\Builders;
use Stitch\DBAL\Schema\Table as Schema;

/**
 * Class Query
 * @package Stitch\DBAL\Builders
 */
class Table
{
    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * Table constructor.
     * @param Schema $schema
     */
    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param Join $join
     * @return $this
     */
    public function join(Join $join)
    {
        $this->joins[] = $join;

        return $this;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function limit(int $count)
    {
        $this->limit = $count;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLimit()
    {
        if ($this->limit !== null) {
            return true;
        }

        foreach ($this->joins as $join) {
            if ($join->hasLimit()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $start
     * @return $this
     */
    public function offset(int $start)
    {
        $this->offset = $start;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOffset()
    {
        if ($this->offset !== null) {
            return true;
        }

        foreach ($this->joins as $join) {
            if ($join->hasOffset()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return array
     */
    public function pullColumns()
    {
        $columns = array_values(array_filter(array_map(function ($column)
        {
            if ($column->isHidden()) {
                return null;
            }

            return (new Column($column))->table($this);
        }, $this->schema->getColumns())));

        foreach ($this->joins as $join) {
            $columns = array_merge($columns, $join->pullColumns());
        }

        return $columns;
    }

    /**
     * @return array
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }
}
