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

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function evaluate()
    {
        $sorter = $this->queryBuilder->getSorter();

        if ($sorter->count()) {
            $this->assembler->push(
                new Component('ORDER BY')
            )->push(
                new Component(implode(', ', array_map(function ($order) {
                    return "{$order['column']->getAlias()} {$order['direction']}";
                }, $sorter->getItems())))
            );
        }
    }
}
