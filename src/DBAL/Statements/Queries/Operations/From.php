<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

/**
 * Class From
 * @package Stitch\DBAL\Statements\Queries\Operations
 */
class From extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * From constructor.
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        parent::__construct();
    }

    /**
     *
     */
    protected function evaluate()
    {
        $this->assembler->push(
            new Component('FROM')
        )->push(
            new Component($this->queryBuilder->getTable())
        );
    }
}