<?php

namespace Stitch\DBAL\Builders;
use Stitch\Schema\Table as Schema;

/**
 * Class Query
 * @package Stitch\DBAL\Builders
 */
class Table
{
    /**
     * @var string
     */
    protected $schema;

    /**
     * @var int
     */
    protected $limit;

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
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return bool
     */
    public function limited()
    {
        if ($this->limit !== null) {
            return true;
        }

        foreach ($this->joins as $join) {
            if ($join->limited()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
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
        $columns = array_map(function ($column)
        {
            return new Column($column);
        }, $this->schema->getColumns());

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
}
