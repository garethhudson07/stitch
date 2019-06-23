<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

class Select extends Statement
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
            new Component('SELECT')
        )->push(
            new Component(
                $this->selection($this->queryBuilder)
            )
        );
    }

    protected function selection(QueryBuilder $queryBuilder)
    {
        $columns = array_map(function (ColumnBuilder $column) use ($queryBuilder)
        {
            $str = "{$queryBuilder->getTable()}.{$column->getName()}";

            if ($alias = $column->getAlias()) {
                $str .= " as $alias";
            }

            return $str;
        }, $queryBuilder->getSelection()->getColumns());

        foreach ($queryBuilder->getJoins() as $join) {
            $columns[] = $this->selection($join);
        }

        return implode(', ', array_filter($columns));
    }
}