<?php

namespace Stitch\DBAL\Builders;

class Query
{
    protected $table;

    protected $primaryKey;

    protected $selection;

    protected $where;

    protected $limit;

    protected $sorter = [];

    protected $joins = [];

    public function __construct(string $table, ?string $primaryKey)
    {
        $this->table = $table;
        $this->primaryKey = $primaryKey ? new Column($primaryKey) : null;
        $this->selection = new Selection();
        $this->where = new Expression();
        $this->sorter = new Sorter();
    }

    public function select(...$arguments)
    {
        $this->selection->unpack($arguments);

        return $this;
    }

    public function join(Join $join)
    {
        $this->joins[] = $join;

        return $this;
    }

    public function where(...$arguments)
    {
        $this->where->and(...$arguments);

        return $this;
    }

    public function whereRaw(...$arguments)
    {
        $this->where->andRaw(...$arguments);

        return $this;
    }

    public function orWhere(...$arguments)
    {
        $this->where->or(...$arguments);

        return $this;
    }

    public function orWhereRaw(...$arguments)
    {
        $this->where->orRaw(...$arguments);

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC')
    {
        $this->sorter->add($column, $direction);

        return $this;
    }

    public function limit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

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

    public function getSelection()
    {
        return $this->selection;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function getWhereConditions()
    {
        return $this->where;
    }

    public function getJoins()
    {
        return $this->joins;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getSorter()
    {
        return $this->sorter;
    }
}