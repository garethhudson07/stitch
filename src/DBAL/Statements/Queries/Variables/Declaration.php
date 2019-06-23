<?php

namespace Stitch\DBAL\Statements\Queries\Variables;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

class Declaration extends Statement
{
    protected $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        parent::__construct();
    }

    protected function evaluate()
    {
        $this->assembler->push(
            new Component('SET')
        )->push(
            new Component(
                implode(', ', $this->variables($this->queryBuilder)) . ';'
            )
        );
    }

    protected function variables(QueryBuilder $queryBuilder)
    {
        $table = $queryBuilder->getTable();
        $limit = $queryBuilder->getLimit();
        $variables = [];

        if ($limit !== null) {
            $variables[] = "@{$table}_{$queryBuilder->getPrimaryKey()->getName()} = NULL";
            $variables[] = "@{$table}_row_num = 0";
        }

        foreach ($queryBuilder->getJoins() as $join) {
            $variables = array_merge($variables, $this->variables($join));
        }

        return $variables;
    }
}