<?php

namespace Stitch\DBAL\Builders;

use Closure;
use Stitch\Schema\Table as Schema;

/**
 * Class Query
 * @package Stitch\DBAL\Builders
 */
class Query extends Table
{
    /**
     * @var Selection
     */
    protected $selection;

    /**
     * @var Expression
     */
    protected $where;

    /**
     * @var array|Sorter
     */
    protected $sorter = [];

    /**
     * Query constructor.
     * @param Schema $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema);

        $this->selection = new Selection();
        $this->sorter = new Sorter();
        $this->where = new Expression();
    }

    /**
     * @param string $path
     * @return $this
     */
    public function select(string $path)
    {
        $this->selection->bind($path);

        return $this;
    }

    /**
     * @param string $path
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $path, string $direction = 'ASC')
    {
        $this->sorter->bind($path, $direction);

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
     * @return Selection
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @return Query
     */
    public function resolve()
    {
        return $this->resolveSelection()
            ->resolveSorter();
    }

    /**
     * @return $this
     */
    public function resolveSelection()
    {
        foreach ($this->selection->getBindings() as $binding) {
            $this->selection->add($this->pullColumn($binding));
        }

        return $this;
    }

    /**
     * @return Column|null
     */
    public function getPrimaryKey()
    {
        return $this->columns->getPrimaryKey();
    }

    /**
     * @return Expression
     */
    public function getWhereConditions()
    {
        return $this->where;
    }

    /**
     * @return array|Sorter
     */
    public function getSorter()
    {
        return $this->sorter;
    }

    /**
     * @return $this
     */
    public function resolveSorter()
    {
        foreach ($this->sorter->getBindings() as $path => $direction) {
            $this->sorter->add($this->pullColumn($path), $direction);
        }

        return $this;
    }
}
