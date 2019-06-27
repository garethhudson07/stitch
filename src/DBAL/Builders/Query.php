<?php

namespace Stitch\DBAL\Builders;

/**
 * Class Query
 * @package Stitch\DBAL\Builders
 */
class Query
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var Column|null
     */
    protected $primaryKey;

    /**
     * @var Selection
     */
    protected $selection;

    /**
     * @var Expression
     */
    protected $where;

    /**
     * @var
     */
    protected $limit;

    /**
     * @var array|Sorter
     */
    protected $sorter = [];

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * Query constructor.
     * @param string $table
     * @param string|null $primaryKey
     */
    public function __construct(string $table, ?string $primaryKey)
    {
        $this->table = $table;
        $this->primaryKey = $primaryKey ? new Column($primaryKey) : null;
        $this->selection = new Selection();
        $this->where = new Expression();
        $this->sorter = new Sorter();
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function select(...$arguments)
    {
        $this->selection->unpack($arguments);

        return $this;
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
     * @param mixed ...$arguments
     * @return $this
     */
    public function where(...$arguments)
    {
        $this->where->and(...$arguments);

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function whereRaw(...$arguments)
    {
        $this->where->andRaw(...$arguments);

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function orWhere(...$arguments)
    {
        $this->where->or(...$arguments);

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function orWhereRaw(...$arguments)
    {
        $this->where->orRaw(...$arguments);

        return $this;
    }

    /**
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'ASC')
    {
        $this->sorter->add($column, $direction);

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
     * @return Selection
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return Column|null
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return Expression
     */
    public function getWhereConditions()
    {
        return $this->where;
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
     * @return array|Sorter
     */
    public function getSorter()
    {
        return $this->sorter;
    }
}