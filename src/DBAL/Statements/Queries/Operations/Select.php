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
                implode(', ', array_map(function ($column)
                {
                    return "{$column->getPath()} as {$column->getAlias()}";
                }, $this->queryBuilder->getSelection()->getColumns()))
            )
        );
    }
}
