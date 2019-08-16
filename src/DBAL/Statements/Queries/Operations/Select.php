<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Select
 * @package Stitch\DBAL\Statements\Queries\Operations
 */
class Select extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Select constructor.
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
            new Component('SELECT')
        )->push(
            new Component(
                implode(', ', $this->columns())
            )
        );
    }

    /**
     * @return array
     */
    protected function columns()
    {
        $selection = $this->queryBuilder->getSelection();

        if ($selection->count()) {
            return $this->map($this->queryBuilder->getSelection()->getColumns());
        }

        return $this->map($this->queryBuilder->pullColumns());
    }

    /**
     * @param array $columns
     * @return array
     */
    protected function map(array $columns)
    {
        return array_map(function ($column)
        {
            return "{$column->getPath()} as {$column->getAlias()}";
        }, $columns);
    }
}
