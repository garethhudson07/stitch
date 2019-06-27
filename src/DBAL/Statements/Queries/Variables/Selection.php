<?php

namespace Stitch\DBAL\Statements\Queries\Variables;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Selection
 * @package Stitch\DBAL\Statements\Queries\Variables
 */
class Selection extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Selection constructor.
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
        $this->assembler->push(
            new Component(implode(', ', $this->variables($this->queryBuilder)))
        );
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return array
     */
    protected function variables(QueryBuilder $queryBuilder)
    {
        $table = $queryBuilder->getTable();
        $limit = $queryBuilder->getLimit();
        $variables = [];

        if ($limit !== null) {
            $column = "{$table}_{$queryBuilder->getPrimaryKey()->getName()}";

            $variables[] = "@{$table}_row_num := if(@{$column} = $column, @{$table}_row_num, @{$table}_row_num + 1) as {$table}_row_number";
            $variables[] = "@{$column} := $column";
        }

        foreach ($queryBuilder->getJoins() as $join) {
            $variables = array_merge($variables, $this->variables($join));
        }

        return $variables;
    }
}