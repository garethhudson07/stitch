<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Queries\Operations\Limit;
use Stitch\DBAL\Statements\Statement;

class Limited extends Statement
{
    protected $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        parent::__construct();
    }

    protected function evaluate()
    {
        $this->queryBuilder->getJoins() ? $this->numbered() : $this->default();
    }

    protected function default()
    {
        $this->assembler->push(
            new Unlimited($this->queryBuilder)
        )->push(
            new Limit($this->queryBuilder)
        );
    }

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
}