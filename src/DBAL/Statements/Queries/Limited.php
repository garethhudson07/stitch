<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Queries\Operations\Limit;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Limited
 * @package Stitch\DBAL\Statements\Queries
 */
class Limited extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Limited constructor.
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function evaluate()
    {
        $this->queryBuilder->getJoins() ? $this->numbered() : $this->default();
    }

    /**
     * @return void
     */
    protected function numbered()
    {
        $this->assembler->push(
            new Component('SELECT * FROM')
        )->push(
            new Subquery(new Numbered($this->queryBuilder), 'numbered')
        )->push(
            new Component('WHERE')
        )->push(
            new Component(implode(' AND ', $this->conditions($this->queryBuilder)))
        );
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return array
     */
    protected function conditions(QueryBuilder $queryBuilder)
    {
        $table = $queryBuilder->getTable();
        $limit = $queryBuilder->getLimit();
        $conditions = [];

        if ($limit !== null) {
            $conditions[] = "{$table}_row_number <= $limit";
        }

        foreach ($queryBuilder->getJoins() as $join) {
            $conditions = array_merge($conditions, $this->conditions($join));
        }

        return $conditions;
    }

    /**
     * @return void
     */
    protected function default()
    {
        $this->assembler->push(
            new Unlimited($this->queryBuilder)
        )->push(
            new Limit($this->queryBuilder)
        );
    }
}