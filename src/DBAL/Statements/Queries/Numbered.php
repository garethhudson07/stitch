<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Queries\Variables\Selection as VariableSelection;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Numbered
 * @package Stitch\DBAL\Statements\Queries
 */
class Numbered extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Numbered constructor.
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
            new Component('SELECT *,')
        )->push(
            new VariableSelection($this->queryBuilder)
        )->push(
            new Component('FROM')
        )->push(
            new Subquery(new Unlimited($this->queryBuilder), 'selection')
        );
    }
}