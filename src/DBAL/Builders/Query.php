<?php

namespace Stitch\DBAL\Builders;

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
    protected $conditions;

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
        $this->conditions = new Expression();
    }

    /**
     * @param Column $column
     * @return $this
     */
    public function select(Column $column)
    {
        $this->selection->add($column);

        return $this;
    }

    /**
     * @param Column $column
     * @param string $direction
     * @return $this
     */
    public function orderBy(Column $column, string $direction = 'ASC')
    {
        $this->sorter->add($column, $direction);

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function where(...$arguments)
    {
        $this->conditions->and(...$arguments);

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function whereRaw(...$arguments)
    {
        $this->conditions->andRaw(...$arguments);

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function orWhere(...$arguments)
    {
        $this->conditions->or(...$arguments);

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function orWhereRaw(...$arguments)
    {
        $this->conditions->orRaw(...$arguments);

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
     * @return Selection
     */
    public function pullSelection()
    {
        $selection = new Selection();

        foreach ($this->pullColumns() as $column) {
            $selection->add($column);
        }

        return $selection;
    }

    /**
     * @return Expression
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @return array|Sorter
     */
    public function getSorter()
    {
        return $this->sorter;
    }
}
