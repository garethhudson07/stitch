<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Assembler;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

/**
 * Class OrderBy
 * @package Stitch\DBAL\Statements\Queries\Operations
 */
class OrderBy extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var Assembler
     */
    protected $orderAssembler;

    /**
     * OrderBy constructor.
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->orderAssembler = (new Assembler())->glue(', ');

        parent::__construct();
    }

    /**
     *
     */
    protected function evaluate()
    {
        $this->sort($this->queryBuilder);

        if ($this->orderAssembler->count()) {
            $this->assembler->push(
                new Component('ORDER BY')
            )->push(
                $this->orderAssembler
            );
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    protected function sort(QueryBuilder $queryBuilder)
    {
        $sorter = $queryBuilder->getSorter();

        if ($sorter->count()) {
            $this->orderAssembler->push(
                new Component(implode(', ', array_map(function ($column) {
                    return "{$column['name']} {$column['direction']}";
                }, $sorter->getColumns())))
            );
        }

        foreach ($queryBuilder->getJoins() as $join) {
            $this->sort($join);
        }
    }
}